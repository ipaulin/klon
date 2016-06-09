<?php
require __DIR__ . '/../vendor/autoload.php';

// Load database configuration
require __DIR__ . '/../src/Paulin/Framex/database.php';

$app = new Paulin\Framex\Application();

$app->bind('Tx\Validator', \Tx\Validator::class);

$app->singleton('Illuminate\Routing\RouteCollection', function() {
    return new \Illuminate\Routing\RouteCollection();
});

$app->run();
