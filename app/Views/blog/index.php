<?php

use CodeIgniter\I18n\Time;

?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto mt-8 px-4">
    <div class="bg-white shadow-lg border rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-center text-blue-600">Blog Posts</h2>
        <p class="text-gray-600 text-center mt-2">Read the latest updates and articles.</p>

        <div class="mt-6 space-y-6">
            <?php foreach ($posts as $post): ?>
                <div class="bg-gray-100 shadow-md border rounded-lg p-4">
                    <h3 class="text-xl font-semibold text-blue-700">
                        <a href="<?= base_url('blog/' . $post['slug']) ?>" class="hover:underline">
                            <?= esc($post['title']) ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Published on <?= Time::parse($post['created_at'])->toLocalizedString('MMM d, yyyy') ?>
                    </p>
                    <p class="mt-2 text-gray-800">
                        <?= (substr($post['content'], 0, 150)) ?>...
                    </p>
                    <div class="mt-4">
                        <a href="<?= base_url('blog/') . $post['slug'] ?>"
                            class="text-blue-500 hover:underline font-semibold">Read More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?= $this->endSection('content') ?>