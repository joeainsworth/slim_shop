<?php

namespace Shop\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Shop\Models\Product;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductController
{
	protected $view;

	public function __construct(Twig $view)
	{
		$this->view = $view;
	}

	public function index($slug, Request $request, Response $response, Product $product, Router $router)
	{
		$product = $product->where('slug', $slug)->first();

		if (!$product) {
			return $response->withRedirect($router->pathFor('home'));
		}

		return $this->view->render($response, 'products/product.twig', [
			'product' => $product,
		]);
	}
}