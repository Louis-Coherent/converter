<?php echo $this->extend('layouts/main'); ?>

<?php echo $this->section('content'); ?>
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="max-w-lg bg-white shadow-lg rounded-2xl p-6 text-center">
        <h1 class="text-2xl font-bold text-green-600">Payment Successful! 🎉</h1>
        <p class="text-gray-600 mt-2">Thank you for your purchase! You now have unlimited conversions.</p>

        <a href="<?= site_url('/') ?>"
            class="mt-4 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">
            Start Converting
        </a>
    </div>
</div>
<?php echo $this->endSection(); ?>