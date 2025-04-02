<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="flex justify-center items-center min-h-screen bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white shadow-lg rounded-2xl p-6">
        <h1 class="text-2xl font-semibold text-gray-700 text-center mb-4"> <?= lang('Auth.register') ?> </h1>

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

        <form action="<?= url_to('register') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.email') ?> </label>
                <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" value="<?= old('email') ?>" required>
            </div>

            <div>
                <label for="username" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.username') ?> </label>
                <input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" value="<?= old('username') ?>" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.password') ?> </label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" required>
            </div>

            <div>
                <label for="password_confirm" class="block text-sm font-medium text-gray-600"> <?= lang('Auth.passwordConfirm') ?> </label>
                <input type="password" name="password_confirm" id="password_confirm" class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition"> <?= lang('Auth.register') ?> </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            <?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>" class="text-blue-600 hover:underline"> <?= lang('Auth.login') ?> </a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>