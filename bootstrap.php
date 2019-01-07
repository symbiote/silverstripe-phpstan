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

// Ensure global $_SERVER exists
global $_SERVER;
if (!$_SERVER) {
    $_SERVER = array();
}

// Mock values in the $_SERVER variable to contain data consistent with the rest of the application.
$_SERVER = array_merge(array(
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'HTTP_ACCEPT' => 'text/plain;q=0.5',
    'HTTP_ACCEPT_LANGUAGE' => '*;q=0.5',
    'HTTP_ACCEPT_ENCODING' => '',
    'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1;q=0.5',
    'SERVER_SIGNATURE' => 'Command-line PHP/' . phpversion(),
    'SERVER_SOFTWARE' => 'PHP/' . phpversion(),
    'SERVER_NAME' => 'localhost',
    'SERVER_ADDR' => '127.0.0.1',
    'REMOTE_ADDR' => '127.0.0.1',
    'REQUEST_METHOD' => 'GET',
    'HTTP_USER_AGENT' => 'CLI',
), $_SERVER);

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
