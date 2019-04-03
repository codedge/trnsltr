<?php

declare(strict_types=1);

$app->add(new Tuupola\Middleware\CorsMiddleware([
    'origin'         => ['*'],
    'methods'        => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    'headers.allow'  => ['Authorization', 'If-Match', 'If-Unmodified-Since', 'Content-Type', 'Accept'],
    'headers.expose' => ['Etag'],
    'credentials'    => true,
    'cache'          => 86400,
    'error'          => function (\Slim\Http\Response $response, array $arguments) {
        $data['status'] = 'Forbidden';
        $data['message'] = $arguments['message'];

        return $response
            ->withStatus(403)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
]));

$app->add(new Tuupola\Middleware\JwtAuthentication([
    'path'   => '/api',
    'secret' => $_ENV['APP_API_SECRET'],
    'error'  => function (\Slim\Http\Response $response, array $arguments) {
        $data['status'] = 'Authentication error';
        $data['message'] = $arguments['message'];

        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
]));
