<?php echo $this->extend('layouts/main'); ?>

<?php echo $this->section('content'); ?>
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="max-w-lg bg-white shadow-lg rounded-2xl p-6 text-center">
        <h1 class="text-2xl font-bold text-red-600">Payment Canceled ❌</h1>
        <p class="text-gray-600 mt-2">It looks like you canceled your payment. You can try again anytime.</p>

        <a href="<?= site_url('pricing') ?>"
            class="mt-4 inline-block bg-gray-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-gray-700">
            Go Back to Pricing
        </a>
    </div>
</div>
<?php echo $this->endSection(); ?>