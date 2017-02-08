<?php

namespace Shop\Models;

use Illuminate\Database\Eloquent\Model;

use Shop\Models\Order;

class Customer extends Model
{
	protected $fillable = [
		'email',
		'name',
	];

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}