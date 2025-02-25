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
            overflow-x: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            /* Scrollable table */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
            /* Ensures it doesn't shrink too much */
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 14px;
            word-break: break-word;
            /* Prevents text overflow */
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

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>IP</th>
                        <th>Filename</th>
                        <th>From</th>
                        <th>To</th>
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
        </div>

        <p class="footer">This is an automated email. Please do not reply.</p>
    </div>
</body>

</html>