<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Conversion Platform</title>

    <!-- Tailwind CSS -->
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="google-site-verification" content="mOFkbvnyla2wygSkeWD7AIbsbpa7X1Qw4rn0KBtcOLA" />
    <!-- Alpine.js (CDN) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body>
    <?= $this->include('partials/_topbar') ?>
    <?= $this->include('partials/_alerts') ?>

    <?= $this->renderSection('content') ?>
</body>

</html>