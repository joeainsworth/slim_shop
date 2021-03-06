<?php

namespace Shop\Controllers;

use Slim\Views\Twig;
use Slim\Router;
use Shop\Basket\Basket;
use Shop\Models\Product;
use Shop\Models\Address;
use Shop\Models\Customer;
use Shop\Models\Order;
use Braintree\Transaction;
use Shop\Validation\Contracts\ValidatorInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Shop\Validation\Forms\OrderForm;

class OrderController
{

	protected $basket;
	protected $router;
	protected $validator;

	public function __construct(Basket $basket, Router $router, ValidatorInterface $validator) 
	{
		$this->basket = $basket;
		$this->router = $router;
		$this->validator = $validator;
	}

	public function index(Request $request, Response $response, Twig $view)
	{
		$this->basket->refresh();

		if (!$this->basket->subTotal()) {
			return $response->withRedirect($this->router->pathFor('cart.index'));
		}

		return $view->render($response, 'order/index.twig');
	}

	public function create(Request $request, Response $response, Customer $customer, Address $address)
	{
		$this->basket->refresh();

		if (!$this->basket->subTotal()) {
			// flash
			return $response->withRedirect($this->router->pathFor('cart.index'));
		}

		if (!$request->getParam('payment_method_nonce')) {
			return $response->withRedirect($this->router->pathFor('order.index'));
		}

		$validation = $this->validator->validate($request, OrderForm::rules());

		if ($validation->fails()) {
			return $response->withRedirect($this->router->pathFor('order.index'));
		}

		$hash = bin2hex(random_bytes(32));

		$customer = $customer->firstOrCreate([
			'email' => $request->getParam('email'),
			'name' => $request->getParam('name'),
		]);

		$address = $address->firstOrCreate([
			'address1' => $request->getParam('address1'),
			'address2' => $request->getParam('address2'),
			'city' => $request->getParam('city'),
			'postal_code' => $request->getParam('postal_code'),
		]);

		$order = $customer->orders()->create([
			'hash' => $hash,
			'paid' => false,
			'total' => $this->basket->subTotal() + 5,
			'address_id' => $address->id,
		]);

		// refactor to reduce queries;
		// $allItems = $this->basket->all();

		// var_dump($this->getQuantities($this->basket->all()));
		// die();

		$order->products()->saveMany(
			$this->basket->all(),
			$this->getQuantities($this->basket->all())
		);

		$result = Transaction::sale([
			'amount' => $this->basket->subTotal() + 5,
			'paymentMethodNonce' => $request->getParam('payment_method_nonce'),
			'options' => [
				'submitForSettlement' => true,
			]
		]);

		$event = new \Shop\Events\OrderWasCreated($order, $this->basket);

		if (!$result->success) {
			$event->attach(new \Shop\Handlers\RecordFailedPayment);
			$event->dispatch();

			return $response->withRedirect($this->router->pathFor('order.index'));
		}

		$event->attach([
			new \Shop\Handlers\MarkOrderPaid,
			new \Shop\Handlers\RecordSuccessfulPayment($result->transaction->id),
			new \Shop\Handlers\UpdateStock,
			new \Shop\Handlers\EmptyBasket,
		]);

		$event->dispatch();

		return $response->withRedirect($this->router->pathFor('order.show', [
			'hash' => $hash,
		]));
	}

	public function show($hash, Request $request, Response $response, Twig $view, Order $order)
	{
		$order = $order->with(['address', 'products'])->where('hash', $hash)->first();

		if (!$order) {
			return $response->withRedirect($this->router->pathFor('home'));
		}

		return $view->render($response, 'order/show.twig', [
			'order' => $order,
		]);
	}

	protected function getQuantities($items)
	{
		$quantities = [];

		foreach ($items as $item) {
			$quantities[] = ['quantity' => $item->quantity];
		}

		return $quantities;
	}
}