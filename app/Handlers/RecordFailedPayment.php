<?php

namespace Shop\Handlers;

use Shop\Handlers\Contracts\HandlerInterface;

class RecordFailedPayment implements HandlerInterface
{
	public function handle($event)
	{
		$event->order->payment()->create([
			'failed' => true,
		]);
	}
}