<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bucket Store</title>
</head>
<body>
    <main>
        <div>
            <form action="" method="post" id="post-form">
                <input type="file" name="file" id="file" required>
                <button type="submit">Upload</button>
            </form>
        </div>
    </main>
    <script>
        const form = document.getElementById('post-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const files = document.querySelector('input[type="file"]');
            if (files.files.length === 0) {
                alert('Please select a file to upload.');
                return;
            }
            else if (files.files.length > 1) {
                alert('Please select only one file to upload.');
                return;
            }
            const file = files.files[0];

            const fileName = file.name;

            const uploadUrlQuery = await fetch(`/f/?filename=${encodeURIComponent(fileName)}`, {
                method: 'POST',
            }).catch((err) => {
                console.error(err);
                alert('An error occurred while uploading the file.');
                return;
            });

            const uploadUrlData = await uploadUrlQuery.json().catch((err) => {
                console.error(err);
                alert('An error occurred while uploading the file.');
                return;
            });

            if (uploadUrlQuery.error !== undefined) {
                console.error(uploadUrlQuery.error);
                alert('An error occurred while uploading the file.');
                return;
            }

            let sendHeader = {
            };

            if (file.type !== '') {
                sendHeader['Content-Type'] = file.type;
            }

            const response = await fetch(uploadUrlData.url, {
                method: 'PUT',
                headers: sendHeader,
                body: file,
            });

            if (response.ok) {
                location.href = `/v/${uploadUrlData.id}`;
            }
            else {
                alert('An error occurred while uploading the file.');
            }
        });
    </script>
</body>
</html>