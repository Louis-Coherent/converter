<?php

use CodeIgniter\I18n\Time;

?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-5xl mx-auto mt-8 px-4">
    <div class="bg-white shadow-lg border rounded-xl p-6">
        <h1 class="text-3xl font-bold text-blue-700"> <?= esc($post['title']) ?> </h1>
        <p class="text-gray-600 text-sm mt-2">Published on <?= Time::parse($post['created_at'])->toLocalizedString('MMM d, yyyy') ?></p>

        <div class="mt-6 text-gray-800 leading-relaxed">
            <?= $post['content'] ?>
        </div>

        <div class="mt-6 text-center">
            <a href="<?= site_url('blog') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-300">
                Back to Blog
            </a>
        </div>
    </div>
</div>

<?= $this->endSection('content') ?>