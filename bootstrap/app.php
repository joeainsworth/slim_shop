<?php

use Shop\App;
use Slim\Views\Twig;
use Braintree\Configuration;
use Illuminate\Database\Capsule\Manager as Capsule;

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new App;

$container = $app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
	'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'shop',
	'username' => 'root',
	'password' => 'm1lkw00d',
	'charset' => 'utf8',
	'collation' => 'utf8_unicode_ci',
	'prefix' => ''
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('mh85bkzqzdft47mm');
Braintree_Configuration::publicKey('f67kpd74zp3wq4w5');
Braintree_Configuration::privateKey('0160053d052f7273a03566ad9725764d');

require __DIR__ . '/../app/routes.php';

$app->add(new \Shop\Middleware\ValidationErrorsMiddleware($container->get(Twig::class)));
$app->add(new \Shop\Middleware\OldInputMiddleware($container->get(Twig::class)));