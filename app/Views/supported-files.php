<?php

use Config\FileConversion;
use Symfony\Component\Mime\MimeTypes;

?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="max-w-4xl mx-auto mt-8 px-4">
    <div class="bg-white shadow-lg border rounded-xl p-6">
        <h2 class="text-2xl font-semibold text-center text-blue-600">Supported Conversions</h2>
        <p class="text-gray-600 text-center mt-2">Easily convert files between these formats.</p>
        <div class="text-center mt-6">
            <a data-umami-event="{Convert Button}" href="<?= base_url('/') ?>"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300">
                Convert Now
            </a>
        </div>
        <div class="mt-6">
            <table class="w-full border-collapse rounded-lg border border-gray-300 text-left">
                <thead class="bg-blue-400 text-white">
                    <tr>
                        <th class="p-3 border border-gray-300">Input Format</th>
                        <th class="p-3 border border-gray-300">Convert To</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-gray-800">
                    <?php
                    // File conversion config
                    $mimeTypes = FileConversion::mimeTypes;

                    // Function to convert MIME type to extension
                    function mimeToExtension($mimeType)
                    {
                        $mimeTypes = new MimeTypes();
                        return strtoupper($mimeTypes->getExtensions($mimeType)[0]) ?? '';
                    }

                    // Loop through file conversion options
                    foreach ($mimeTypes as $mimeType => $extensions) {
                        $extMain = mimeToExtension($mimeType);
                        echo "<tr class='border border-gray-300'>";
                        echo "<td class='p-3 border border-gray-300 font-semibold text-blue-700'>" . strtoupper($extMain) . "</td>";
                        echo "<td class='p-3 border border-gray-300'>" .
                            implode(', ', array_map(function ($ext) use ($extMain) {
                                return "<a href='" . strtolower($extMain) . "-to-" . htmlspecialchars($ext) . "' class='text-blue-500 hover:underline'>" . strtoupper($ext) . "</a>";
                            }, $extensions)) .
                            "</td>";
                        echo "</tr>";
                    }

                    ?>
                </tbody>
            </table>
        </div>

        <!-- Convert Now Button -->
        <div class="text-center mt-6">
            <a data-umami-event="{Convert Button}" href="<?= base_url('/') ?>"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow-md transition duration-300">
                Convert Now
            </a>
        </div>

    </div>
</div>

<?= $this->endSection('content') ?>