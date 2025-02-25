<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>File Shift - Fast & Secure File Conversion Platform</title>
    <meta name="description"
        content="File Shift is a fast and secure online platform that helps you convert your files between different formats with ease. Start converting today!">
    <meta name="keywords"
        content="file conversion, convert files, online file converter, pdf to word, image to pdf, audio conversion, video converter">
    <link rel="icon" href="<?= base_url('file-shift.ico') ?>">

    <!-- Open Graph Meta Tags for Social Media (Facebook, LinkedIn, etc.) -->
    <meta property="og:title" content="File Shift - Fast & Secure File Conversion Platform">
    <meta property="og:description"
        content="Convert files quickly and securely on File Shift. From documents to images, our platform supports a variety of formats for your conversion needs.">
    <meta property="og:image" content="<?= base_url('file-shift-logo.png') ?>">
    <!-- Replace with actual image URL -->
    <meta property="og:url" content="https://file-shift.com">
    <meta property="og:type" content="website">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:title" content="File Shift - Fast & Secure File Conversion Platform">
    <meta name="twitter:description"
        content="Convert files quickly and securely on File Shift. Easily handle various file formats with our fast conversion platform.">
    <meta name="twitter:image" content="<?= base_url('file-shift-logo.png') ?>">
    <!-- Replace with actual image URL -->
    <meta name="twitter:card" content="summary_large_image">

    <!-- Additional Meta Tags for better SEO and usability -->
    <meta name="author" content="File Shift">

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