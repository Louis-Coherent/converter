<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex items-center justify-center h-screen">
    <div class="text-center bg-white shadow-xl rounded-2xl p-8 max-w-md">
        <h1 class="text-4xl font-bold text-red-500">404</h1>
        <p class="text-lg text-gray-600 mt-4">Oops! The page you're looking for doesn't exist.</p>
        <a href="/" class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition">
            Go Back Home
        </a>
    </div>
</div>

<?= $this->endSection() ?>