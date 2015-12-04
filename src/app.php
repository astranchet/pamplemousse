<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\SessionServiceProvider;

$app = new Silex\Application();

/**  Application */

$app->register(new SessionServiceProvider());

/** Controller */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/', function() use($app) {
    return new Response();
});


return $app;
