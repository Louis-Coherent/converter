<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'File::index');

$routes->group('file', function ($routes) {
    $routes->post('allowed-conversions', 'File::allowedConversions', ['filter' => 'ajax']);
    $routes->post('upload', 'File::upload', ['filter' => 'ajax']);
    $routes->post('status', 'File::status', ['filter' => 'ajax']);
    $routes->post('remove', 'File::remove');
    $routes->get('download/(:any)', 'File::downloadSingle/$1');
    $routes->get('download-zip', 'File::downloadMultiple');
});

$routes->get('supported-files', function () {
    return view('supported-files');
});


$routes->cli('test', 'Dev::test');
$routes->cli('test-email', 'Dev::testSendEmail');
