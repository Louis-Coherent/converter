<!DOCTYPE html>
<html lang="en">

<?php

helper('inflector');

$sitePath = current_url(true)->getRoutePath();

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title><?= ($title) ?? humanize($sitePath) ?> – File Shift</title>
    <meta name="title" content="<?= ($metaTitle) ?? '' ?> | File Shift">
    <meta name="description"
        content="File Shift is a fast and secure online platform that helps you convert your files between different formats with ease. Start converting today!">
    <link rel="icon" href="<?= base_url('file-shift.ico') ?>">
    <script defer src="https://cloud.umami.is/script.js" data-website-id="caba49ba-759b-4f5b-8baf-0963c1917845">
    </script>
    <!-- Open Graph Meta Tags for Social Media (Facebook, LinkedIn, etc.) -->
    <meta property="og:title" content="<?= ($title) ?? '' ?> – File Shift">
    <meta property="og:description"
        content="Convert files quickly and securely on File Shift. From documents to images, our platform supports a variety of formats for your conversion needs.">
    <meta property="og:image" content="<?= base_url('file-shift-logo.png') ?>">
    <!-- Replace with actual image URL -->
    <meta property="og:url" content="https://file-shift.com">
    <meta property="og:type" content="website">
    <link rel="canonical" href="<?= current_url(); ?>" />
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1295496956350675"
        crossorigin="anonymous"></script>
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:title" content="<?= ($title) ?? '' ?> – File Shift">
    <meta name="twitter:description"
        content="Convert files quickly and securely on File Shift. Easily handle various file formats with our fast conversion platform.">
    <meta name="twitter:image" content="<?= base_url('file-shift-logo.png') ?>">
    <!-- Replace with actual image URL -->
    <meta name="twitter:card" content="<?= base_url('file-shift-logo.png') ?>">

    <!-- Additional Meta Tags for better SEO and usability -->
    <meta name="author" content="File Shift">

    <!-- Tailwind CSS -->
    <?php if (ENVIRONMENT == 'production'): ?>
        <link href="<?= base_url('assets/css/style.min.css') ?>" rel="stylesheet">
    <?php else: ?>
        <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <?php endif; ?>


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

<body class="max-h-[400px] overflow-y-auto
  [&::-webkit-scrollbar]:w-2
  [&::-webkit-scrollbar-track]:rounded-full
  [&::-webkit-scrollbar-track]:bg-gray-100
  [&::-webkit-scrollbar-thumb]:rounded-full
  [&::-webkit-scrollbar-thumb]:bg-gray-300">
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Service",
            "name": "Online File Converter",
            "description": "Convert documents, audio, video, and images into different formats online.",
            "provider": {
                "@type": "Organization",
                "name": "File Shift",
                "url": "https://file-shift.com"
            },
            "areaServed": {
                "@type": "Country",
                "name": "Worldwide"
            },
            "serviceType": "File Conversion",
            "availableChannel": {
                "@type": "Website",
                "url": "https://file-shift.com/converter"
            }
        }
    </script>

    <?= $this->include('partials/_topbar') ?>
    <?= $this->include('partials/_alerts') ?>

    <?= $this->renderSection('content') ?>
    <?= $this->include('partials/_footer') ?>

</body>

</html>