<?php

namespace Shop\Handlers;

use Shop\Handlers\Contracts\HandlerInterface;

class RecordSuccessfulPayment implements HandlerInterface
{
	protected $transactionId;

	public function __construct($transactionId)
	{
		$this->transactionId = $transactionId;
	}

	public function handle($event)
	{
		$event->order->payment()->create([
			'failed' => false,
			'transaction_id' => $this->transactionId,
		]);
	}
}