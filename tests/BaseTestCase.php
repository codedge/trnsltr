<?php

namespace Tests;

use Dotenv\Dotenv;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class BaseTestCase extends TestCase
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var App
     */
    protected $app;

    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->createApplication();

        $this->logger = new Logger('test');
        $this->logger->pushHandler(new TestHandler());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->app = null;
    }

    /**
     * Create application.
     */
    protected function createApplication(): void
    {
        $dotenv = Dotenv::create(__DIR__ . '/..');
        $dotenv->load();

        // Use the application settings
        $settings = require __DIR__ . '/../src/settings.php';
        // Instantiate the application
        $this->app = $app = new App($settings);
        // Set up dependencies
        require __DIR__ . '/../src/dependencies.php';
        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../src/middleware.php';
        }
        // Register routes
        require __DIR__ . '/../src/routes.php';
    }

    /**
     * Make a request to the Api
     *
     * @param       $requestMethod
     * @param       $requestUri
     * @param null $requestData
     * @param array $headers
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function request($requestMethod, $requestUri, $requestData = null, $headers = [])
    {
        return $this->runApp($requestMethod, $requestUri, $requestData, $headers);
    }

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     *
     * @param array $headers
     *
     * @return \Psr\Http\Message\ResponseInterface|\Slim\Http\Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function runApp($requestMethod, $requestUri, $requestData = null, $headers = [])
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            array_merge(
                [
                    'SERVER_NAME'      => $_ENV['APP_URL'],
                    'HTTP_HOST'        => $_ENV['APP_URL'],
                    'REQUEST_METHOD'   => $requestMethod,
                    'REQUEST_URI'      => $requestUri,
                    'Content-Type'     => 'application/json',
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
                $headers
            )
        );
        
        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);
        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();
        
        // Process the application and Return the response
        return $this->app->process($request, $response);
    }

}
