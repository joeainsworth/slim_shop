<?php

namespace Shop\Validation;

use Shop\Validation\Contracts\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class Validator implements ValidatorInterface
{
	protected $basket;
	protected $router;
	
	public function validate(Request $request, array $rules)
	{
		
	}

	public function fails()
	{
		return false;
	}
}