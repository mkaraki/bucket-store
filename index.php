<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/_config.php';

$klein = new \Klein\Klein();

use Aws\S3\PostObjectV4;
use Aws\S3\S3Client;

function getS3()
{
    return $s3 = new Aws\S3\S3Client([
        'region' => S3_REGION,
        'endpoint' => S3_ENDPOINT,
        'credentials' => [
            'key' => S3_KEY,
            'secret' => S3_SECRET,
        ],
        'version' => 'latest',
        'use_path_style_endpoint' => true,
    ]);
}

$klein->respond('/', function ($request, $response, $service, $app) {
    $service->render('views/index.php');
});

$klein->respond('POST', '/f/', function ($request, $response, $service, $app) {
    $id = uniqid(rand(), true);
    
    $s3 = getS3();

    $postCommand = $s3->getCommand('PutObject', [
        'Bucket' => S3_BUCKET,
        'Key' => $id,
    ]);
    $preSignRequest = $s3->createPresignedRequest($postCommand, '+60 minutes');
    try
    {
        $presignedUrl = (string) $preSignRequest->getUri();
        $response->json([
            'url' => $presignedUrl,
            'id' => $id,
        ]);
    }
    catch (Exception $e)
    {
        $response->json(['error' => $e->getMessage()]);
    }
});

$klein->respond('GET', '/f/[:id]', function ($request, $response, $service, $app) {
    $id = $request->id;

    $s3 = getS3();

    try
    {
        $head = $s3->headObject([
            'Bucket' => S3_BUCKET,
            'Key' => $id,
        ]);
    }
    catch (Aws\S3\Exception\S3Exception $e)
    {
        $response->code(404);
        return;
    }

    $getCommand = $s3->getCommand('GetObject', [
        'Bucket' => S3_BUCKET,
        'Key' => $id,
    ]);
    $presignedUrl = $s3->createPresignedRequest($getCommand, '+120 minutes');
    try
    {
        $presignedUrl = (string) $presignedUrl->getUri();
        $response->redirect($presignedUrl, 302);
    }
    catch (Exception $e)
    {
        $response->code(404);
    }
});

$klein->respond('GET', '/d/[:id]', function ($request, $response, $service, $app) {
    $id = $request->id;

    $service->render('views/uploaded.php', [
        'id' => $id,
    ]);
});


$klein->dispatch();