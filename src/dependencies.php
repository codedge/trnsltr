<?php declare(strict_types = 1);

use App\Http\Controller\TranslateController;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

// Redis cache
$container['cache'] = function (ContainerInterface $c) {
    $config = [
        'schema' => $c->get('settings')['redis']['schema'],
        'host' => $c->get('settings')['redis']['host'],
        'port' => $c->get('settings')['redis']['port'],
        'database' => $c->get('settings')['redis']['database'],
    ];

    return new Predis\Client($config);
};

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Request validator
$container['validator'] = function () {
    return new Awurth\SlimValidation\Validator();
};

// Controller
$container[TranslateController::class] = function (ContainerInterface $c): TranslateController {
    $logger = $c->get('logger');
    $cache = $c->get("cache");
    $validator = $c->get("validator");

    $guzzle = new \GuzzleHttp\Client(
        [
        'base_uri' => $c->get('settings')['deepl']['api_host'],
        ]
    );

    $settings = $c->get('settings')['deepl'];
    
    $repository = new \App\Repository\TranslateRepository($c, $guzzle, $settings, $logger);

    return new TranslateController($validator, $repository);
};