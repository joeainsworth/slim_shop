<?php

use function DI\get;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Shop\Basket\Basket;
use Shop\Models\Product;
use Shop\Support\Storage\SessionStorage;
use Interop\Container\ContainerInterface;
use Shop\Support\Storage\Contracts\StorageInterface;

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

		$twig->getEnvironment()->addGlobal('basket', $c->get(Basket::class));

		return $twig;
	},
	Product::class => function (ContainerInterface $c) {
		return new Product;
	},
	Basket::class => function (ContainerInterface $c) {
		return new Basket(
			$c->get(SessionStorage::class),
			$c->get(Product::class)
		);
	}
];