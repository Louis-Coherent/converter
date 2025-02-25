<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Recent File Conversions</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
    }

    .email-container {
        max-width: 600px;
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: auto;
    }

    h2 {
        text-align: center;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
        font-size: 12px;
        color: #777;
    }
    </style>
</head>

<body>
    <div class="email-container">
        <h2>Recent File Conversions</h2>
        <p>Here are the files converted in the last 5 minutes:</p>

        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>IP</th>
                    <th>Filename</th>
                    <th>From Format</th>
                    <th>To Format</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= $file['id'] ?></td>
                    <td><?= $file['ip'] ?></td>
                    <td><?= $file['og_file_name'] ?></td>
                    <td><?= $file['format_from'] ?></td>
                    <td><?= $file['format_to'] ?></td>
                    <td><?= $file['status'] ?></td>
                    <td><?= $file['updated_at'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p class="footer">This is an automated email. Please do not reply.</p>
    </div>
</body>

</html>