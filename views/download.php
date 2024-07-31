<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download - Bucket Store</title>
</head>
<body>
    <div id="dlmsg">
        Download starts shortly.
    </div>

    <table>
        <tbody>
            <tr>
                <th scope="row">File id</th>
                <td><?= htmlentities($this->id) ?></td>
            </tr>
            <tr>
                <th scope="row">Filename</th>
                <td><?= htmlentities($this->filename) ?></td>
            </tr>
        </tbody>
    </table>

    <script>
        const datas = <?= json_encode([$this->filename, $this->url]) ?>;
        const dlmsg = document.getElementById('dlmsg');

        fetch(datas[1], {
            method: 'GET'
        })
        .then(v => v.blob())
        .then(blob => {
            const objUrl = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            a.href = objUrl;
            a.download = datas[0];
            a.innerText = 'Click here if the download does not start automatically.';
            dlmsg.appendChild(a);
            a.click();    
        });
    </script>
</body>
</html>