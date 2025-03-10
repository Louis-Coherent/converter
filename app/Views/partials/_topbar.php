<div x-data="{ open: false, dropdown: false }" class="bg-blue-600 text-white p-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo and Site Name -->
        <a href="<?= base_url() ?>" class="flex items-center space-x-2">
            <h1 class="text-3xl font-extrabold tracking-wide">File Shift</h1>
        </a>

        <!-- Desktop Navigation (Visible on medium screens and larger) -->
        <nav class="hidden md:flex space-x-6 items-center">
            <a href="<?= base_url('/') ?>" class="hover:underline">Convert</a>
            <a href="<?= base_url('/supported-files') ?>" class="hover:underline">Conversions</a>
            <a href="<?= base_url('/blog') ?>" class="hover:underline">Blog</a>

            <!-- Premium Notice -->
            <?php if (!auth()->user() || auth()->user()->toRawArray()['is_premium'] == 0) : ?>
                <a href="<?= url_to('pricing') ?>" class="bg-yellow-400 text-black px-4 py-2 rounded-lg shadow-md hover:bg-yellow-500 font-semibold">
                    🚀 Upgrade to Unlimited
                </a>
            <?php endif; ?>

            <!-- Auth Navigation -->
            <?php if (!auth()->user()) : ?>
                <a href="<?= url_to('login') ?>" class="flex items-center space-x-1 hover:underline">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Login</span>
                </a>
                <a href="<?= url_to('register') ?>" class="flex items-center space-x-1 hover:underline">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>Sign Up</span>
                </a>
            <?php else: ?>
                <!-- Profile Dropdown -->
                <div class="relative" @click.away="dropdown = false">
                    <button @click="dropdown = !dropdown" class="flex items-center space-x-2 focus:outline-none">
                        <i class="fa-solid fa-user"></i>
                        <span><?= esc(auth()->user()->username) ?></span>
                        <i class="fa-solid fa-chevron-down text-sm"></i>
                    </button>

                    <div x-show="dropdown" class="absolute right-0 mt-2 w-48 bg-white text-black rounded-lg shadow-lg py-2">
                        <form action="<?= url_to('logout') ?>" method="post" class="block">
                            <?= csrf_field() ?>
                            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200">Logout</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </nav>

        <!-- Mobile Menu Button (Visible only on small screens) -->
        <button @click="open = !open" class="md:hidden focus:outline-none">
            <i class="bi bi-list text-2xl"></i>
        </button>
    </div>

    <!-- Mobile Navigation (Hidden by default, shown when toggled) -->
    <div x-show="open" class="md:hidden bg-blue-700 p-4 mt-2 rounded-lg">
        <a href="<?= base_url('/') ?>" class="block py-2 hover:underline">Convert</a>
        <a href="<?= base_url('/supported-files') ?>" class="block py-2 hover:underline">Conversions</a>
        <a href="<?= base_url('/blog') ?>" class="block py-2 hover:underline">Blog</a>

        <!-- Premium Notice -->
        <?php if (!auth()->user() || auth()->user()->toRawArray()['is_premium'] == 0) : ?>
            <a href="<?= url_to('pricing') ?>" class="block py-2 text-center bg-yellow-400 text-black px-4 py-2 rounded-lg shadow-md hover:bg-yellow-500 font-semibold">
                🚀 Upgrade to Unlimited
            </a>
        <?php endif; ?>

        <!-- Mobile Auth Links -->
        <?php if (!auth()->user()) : ?>
            <a href="<?= url_to('login') ?>" class="block py-2 hover:underline flex items-center">
                <i class="bi bi-box-arrow-in-right mr-1"></i>
                Login
            </a>
            <a href="<?= url_to('register') ?>" class="block py-2 hover:underline flex items-center">
                <i class="fa-solid fa-user-plus"></i>
                Sign Up
            </a>
        <?php else: ?>
            <form action="<?= url_to('logout') ?>" method="post" class="block">
                <?= csrf_field() ?>
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-200 flex items-center">
                    <i class="bi bi-box-arrow-right mr-1"></i>
                    Logout
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Subtitle -->
    <h2 class="text-lg font-light text-center mt-1 opacity-90">Fast & seamless file conversion, anytime.</h2>
</div>