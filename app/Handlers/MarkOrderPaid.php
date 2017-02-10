<?php

namespace Shop\Handlers;

use Shop\Handlers\Contracts\HandlerInterface;

class MarkOrderPaid implements HandlerInterface
{
	public function handle($event)
	{
		$event->order->update([
			'paid' => true
		]);
	}
}