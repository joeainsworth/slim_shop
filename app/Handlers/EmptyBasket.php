<?php

namespace Shop\Handlers;

use Shop\Handlers\Contracts\HandlerInterface;

class EmptyBasket implements HandlerInterface
{
	public function handle($event)
	{
		$event->basket->clear();
	}
}