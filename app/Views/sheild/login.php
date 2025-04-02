<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-2xl font-semibold text-gray-700 text-center mb-4"> <?= lang('Auth.login') ?> </h1>

        <?php if (session('error') !== null) : ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"> <?= session('error') ?> </div>
        <?php elseif (session('errors') !== null) : ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
                <?php if (is_array(session('errors'))) : ?>
                    <?php foreach (session('errors') as $error) : ?>
                        <p><?= $error ?></p>
                    <?php endforeach ?>
                <?php else : ?>
                    <p><?= session('errors') ?></p>
                <?php endif ?>
            </div>
        <?php endif ?>

        <?php if (session('message') !== null) : ?>
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm"> <?= session('message') ?> </div>
        <?php endif ?>

        <form action="<?= url_to('login') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.email') ?> </label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" value="<?= old('email') ?>" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.password') ?> </label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" required>
            </div>

            <?php if (setting('Auth.sessionConfig')['allowRemembering']) : ?>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="mr-2" <?php if (old('remember')): ?> checked<?php endif ?>>
                    <label for="remember" class="text-sm text-gray-600"> <?= lang('Auth.rememberMe') ?> </label>
                </div>
            <?php endif; ?>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"> <?= lang('Auth.login') ?> </button>
        </form>

        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
            <p class="text-center text-sm text-gray-600 mt-4">
                <?= lang('Auth.forgotPassword') ?> <a href="<?= url_to('magic-link') ?>" class="text-blue-600 hover:underline"> <?= lang('Auth.useMagicLink') ?> </a>
            </p>
        <?php endif ?>

        <?php if (setting('Auth.allowRegistration')) : ?>
            <p class="text-center text-sm text-gray-600 mt-2">
                <?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>" class="text-blue-600 hover:underline"> <?= lang('Auth.register') ?> </a>
            </p>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>