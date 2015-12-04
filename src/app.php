<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;

$app = new Silex\Application();

/**  Application */

$app->register(new SessionServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

/** Controller */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->get('/', Pamplemousse\Controller::class.'::indexAction');

return $app;
