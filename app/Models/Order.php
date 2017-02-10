<?php

namespace Shop\Models;

use Illuminate\Database\Eloquent\Model;

use Shop\Models\Address;
use Shop\Models\Customer;
use Shop\Models\Payment;
use Shop\Models\Product;

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
		return $this->belongsTo(Address::class);
	}

	public function products()
	{
		return $this->belongsToMany(Product::class, 'orders_products')->withPivot('quantity');
	}

	public function payment()
	{
		return $this->hasOne(Payment::class);
	}
}