<?php

namespace App\Exceptions\Cart;

use RuntimeException;

class CartProductLimitExceededException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cart product limit exceeded.');
    }
}
