<?php declare(strict_types = 1);

return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => false,
        'displayErrorDetails' => $_ENV['APP_ENV'] === 'production' ? false : true,
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // JWT (Json Web Tokens)
        'jwt' => [
            'secret' => $_ENV['APP_API_SECRET'] ?? 'secret',
        ],

        // Database
        'database' => [
            'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host' => $_ENV['DB_HOST'] ?? 'localhost',
            'database' => $_ENV['DB_NAME'] ?? 'trnsltr',
            'username' => $_ENV['DB_USER'] ?? 'trnsltr',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => $_ENV['DB_PREFIX'] ?? '',
        ],

        // Redis cache
        'redis' => [
            'schema' => 'tcp',
            'host' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['REDIS_PORT'] ?? 6379,
            'database' => $_ENV['REDIS_DATABASE'] ?? 0,
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // DeepL translator
        'deepl' => [
            'api_host' => $_ENV['DEEPL_API_HOST'] ?? 'https://api.deepl.com',
            'api_token' => $_ENV['DEEPL_API_TOKEN'],
            'api_version' => 'v2',
        ]
    ],
];
