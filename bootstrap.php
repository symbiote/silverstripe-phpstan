<?php

use SilverStripe\Core\CoreKernel;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Control\HTTPRequestBuilder;
use SilverStripe\Core\Startup\ErrorControlChainMiddleware;
use SilverStripe\Control\HTTPResponse_Exception;

// Find Composer autoload.php
if (file_exists(__DIR__ . '/../../autoload.php')) {
    require __DIR__ . '/../../autoload.php';
} elseif (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo "autoload.php not found";
    exit(1);
}

// Mock values to be empty if not set
if (!isset($_SERVER['REQUEST_URI'])) { $_SERVER['REQUEST_URI'] = ''; }
if (!isset($_SERVER['REQUEST_METHOD'])) { $_SERVER['REQUEST_METHOD'] = ''; }

// Default application
try {
    $kernel = new CoreKernel(BASE_PATH);
    $kernel->boot();
} catch (HTTPResponse_Exception $e) {
    // ignore unconfigured DB error from CoreKernel::redirectToInstaller()
}
//$app = new HTTPApplication($kernel);
//$app->addMiddleware(new ErrorControlChainMiddleware($app));
//$request = HTTPRequestBuilder::createFromEnvironment(); // Build request and detect flush
//$response = $app->handle($request);
//$response->output();
