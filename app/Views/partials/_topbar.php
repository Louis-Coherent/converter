<div x-data="{ open: false }" class="bg-blue-400 text-white p-4">
    <div class="max-w-4xl mx-auto flex items-center justify-between">
        <!-- Logo and Site Name -->
        <a href="<?= base_url() ?>" class="flex items-center space-x-2">
            <h1 class="text-3xl font-extrabold tracking-wide">File Shift</h1>
        </a>

        <!-- Desktop Navigation (Visible on medium screens and larger) -->
        <nav class="hidden md:flex space-x-6">
            <a href="<?= base_url('/') ?>" class="hover:underline">Convert</a>
            <a href="<?= base_url('/supported-files') ?>" class="hover:underline">Conversions</a>
            <a href="<?= base_url('/guide-to-converting-files') ?>" class="hover:underline">Guide</a>
        </nav>

        <!-- Mobile Menu Button (Visible only on small screens) -->
        <button @click="open = !open" class="md:hidden focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile Navigation (Hidden by default, shown when toggled) -->
    <div x-show="open" class="md:hidden bg-blue-500 p-4 mt-2 rounded-lg">
        <a href="<?= base_url('/') ?>" class="block py-2 hover:underline">Convert</a>
        <a href="<?= base_url('/supported-files') ?>" class="block py-2 hover:underline">Conversions</a>
        <a href="<?= base_url('/guide-to-converting-files') ?>" class="block py-2 hover:underline">Guide</a>
    </div>

    <!-- Subtitle -->
    <h2 class="text-lg font-light text-center mt-1 opacity-90">Fast & seamless file conversion, anytime.</h2>
</div>