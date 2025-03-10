<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md bg-white shadow-lg rounded-2xl p-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Unlimited Conversions</h1>
        <p class="text-gray-600 mt-2">Get unlimited access for only <span class="font-semibold">$10</span></p>

        <a href="<?= site_url('payment/checkout') ?>"
            class="mt-4 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700">
            Upgrade for $10
        </a>

        <p class="mt-4 text-gray-500 text-sm">One-time payment. No subscriptions.</p>
    </div>
</div>
<?= $this->endSection() ?>