<?php

namespace Shop\Controllers;

use Slim\Router;
use Slim\Views\Twig;
use Shop\Models\Product;
use Shop\Basket\Basket;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Shop\Basket\Exceptions\QuantityExceededException;

class CartController
{
	protected $basket;
	protected $product;

	public function __construct(Basket $basket, Product $product)
	{
		$this->basket = $basket;
		$this->product = $product;
	}

	public function index(Request $request, Response $response, Twig $view)
	{
		// TO DO: flash message if basket refresh occurs
		$this->basket->refresh();

		return $view->render($response, 'cart/index.twig');
	}

	public function add($slug, $quantity, Request $request, Response $response, Router $router)
	{
		$product = $this->product->where('slug', $slug)->first();

		if (!$product) {
			return $response->withRedirect($router->pathFor('home'));
		}

		try {
			// TO DO: flash message if more product added
			$this->basket->add($product, $quantity);
		} catch (QuantityExceededException $e) {
			// TO DO: flash message if no more product is available
		}

		return $response->withRedirect($router->pathFor('cart.index'));
	}

	public function update($slug, Request $request, Response $response, Router $router)
	{
		$product = $this->product->where('slug', $slug)->first();

		if (!$product) {
			return $response->withRedirect($router->pathFor('home'));
		}

		try {
			$this->basket->update($product, $request->getParam('quantity'));
		} catch (QuantityExceededException $e) {
			// 
		}

		return $response->withRedirect($router->pathFor('cart.index'));
	}
}