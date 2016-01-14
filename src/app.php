<?php
require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use DerAlex\Silex\YamlConfigServiceProvider;

$app = new Silex\Application();

/** Configuration */
$app->register(new YamlConfigServiceProvider(__DIR__.'/../config/app.yml'));

/**  Application */
$app->register(new DoctrineServiceProvider(), array(
    'db.options' => $app['config']['database']
));
$app->register(new FormServiceProvider());
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile'    => __DIR__ . '/../log/app.log',
    'monolog.name'       => 'pamplemousse',
    'monolog.level'      => 100
));
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
            'http' => true,
            'users' => array(
                // raw password is foo
                'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
            ),
        )
    )
));
$app->register(new SessionServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'translator.messages' => array(),
));
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app['twig'] = $app->share($app->extend('twig', function($twig) {
    $twig->addExtension(new Pamplemousse\Twig_Extension\TimeAgoFilter());
    return $twig;
}));

$app->register(new UrlGeneratorServiceProvider());

/** Services */
$app['photos'] = $app->share(function ($app) {
    return new Pamplemousse\Photos\Service($app);
});
$app['slug'] = $app->share(function ($app) {
    return new Cocur\Slugify\Slugify();
});
$app['imagine'] = $app->share(function ($app) {
    $imagine = new \Imagine\Gd\Imagine();
    $imagine->setMetadataReader(new \Imagine\Image\Metadata\ExifMetadataReader());
    return $imagine;
});

/** Controller */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->mount('/', new Pamplemousse\Router());
$app->mount('/photos/', new Pamplemousse\Photos\Router());
$app->mount('/admin/', new Pamplemousse\Admin\Router());

return $app;
