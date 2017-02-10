<?php

namespace Shop\Events;

use Shop\Models\Order;
use Shop\Basket\Basket;

class OrderWasCreated extends Event
{
	public $order;
	public $basket;

	public function __construct(Order $order, Basket $basket) 
	{
		$this->order = $order;
		$this->basket = $basket;
	}
}