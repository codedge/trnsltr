<?php

declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$settings = require __DIR__.'/src/settings.php';
$app = new \Slim\App($settings);
// Set up dependencies
require __DIR__.'/src/dependencies.php';

$db = $container['settings']['database'];

return [
    'paths' => [
        'migrations' => 'src/Migrations',
        'seeds'      => 'src/Seeds',
    ],
    'environments' => [
        'default' => [
            'adapter' => $db['driver'],
            'host'    => $db['host'],
            'name'    => $db['database'],
            'user'    => $db['username'],
            'pass'    => $db['password'],
        ],
        'default_migration_table' => 'migrations',
    ],
    'migration_base_class' => "App\Migrations\Migration",
    'templates'            => [
        'file' => 'src/Migrations/Migration.template.php.dist',
    ],
];
