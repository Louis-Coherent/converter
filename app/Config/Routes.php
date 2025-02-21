<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'File::index');

$routes->group('file', function ($routes) {
    $routes->post('upload', 'File::upload', ['filter' => 'ajax']);
    $routes->post('status', 'File::status', ['filter' => 'ajax']);
});
