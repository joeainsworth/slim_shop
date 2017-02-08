<?php

namespace Shop\Models;

use Illuminate\Database\Eloquent\Model;

use Shop\Models\Customer;
use Shop\Models\Address;

class Order extends Model
{
	protected $fillable = [
		'hash',
		'total',
		'paid',
		'address_id',
	];

	public function address()
	{
		return $this->belongsTo(Address:class);
	}

	public function products()
	{
		$this->belongsToMany(Products::class, 'orders_products')->withPivot('quantity');
	}
}