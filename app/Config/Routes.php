<?php

use Config\FileConversion;
use Symfony\Component\Mime\MimeTypes;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'File::index');

$routes->group('file', function ($routes) {
    $routes->post('allowed-conversions', 'File::allowedConversions', ['filter' => 'noindex']);
    $routes->post('upload', 'File::upload', ['filter' => 'noindex']);
    $routes->post('status', 'File::status', ['filter' => 'noindex']);
    $routes->post('remove', 'File::remove', ['filter' => 'noindex']);
    $routes->get('download/(:any)', 'File::downloadSingle/$1', ['filter' => 'noindex']);
    $routes->get('download-zip', 'File::downloadMultiple', ['filter' => 'noindex']);
});

$routes->get('supported-files', function () {
    return view('supported-files');
});

// Your mimeTypes array from FileConversion
$mimeTypes = FileConversion::mimeTypes;


// Loop through mimeTypes and extensions to generate routes manually
foreach ($mimeTypes as $mimeType => $extensions) {
    $mimeTypes = new MimeTypes();

    $mimeType = ($mimeTypes->getExtensions($mimeType)[0]) ?? '';
    foreach ($extensions as $extension) {

        // Define a route for each conversion (crawlable)
        $routes->get("{$mimeType}-to-{$extension}", 'File::index/' . $mimeType . '/' . $extension);
    }
}



$routes->cli('test', 'Dev::test');
$routes->cli('test-email', 'Dev::testSendEmail');
