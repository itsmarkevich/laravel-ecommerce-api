<?php

namespace App\Exceptions\Cart;

use RuntimeException;

class CartItemNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cart item not found.');
    }
}
