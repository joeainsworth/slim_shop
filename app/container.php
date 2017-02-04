<?php

use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Shop\Models\Product;
use Interop\Container\ContainerInterface;
use Shop\Support\Storage\SessionStorage;
use Shop\Support\Storage\Contracts\StorageInterface;
use function DI\get;

return [
	'router' => get(Slim\Router::class),
	StorageInterface::class => function (ContainerInterface $c) {
		return new SessionStorage('cart');
	},
	Twig::class => function (ContainerInterface $c) {
		$twig = new Twig(__DIR__ . '/resources/views', [
			'cache' => false
		]);

		$twig->addExtension(new TwigExtension(
			$c->get('router'),
			$c->get('request')->getUri()
		));

		return $twig;
	},
	Product::class => function (ContainerInterface $c) {
		return new Product;
	}
];