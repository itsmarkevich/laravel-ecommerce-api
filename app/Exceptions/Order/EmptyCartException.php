<?php

namespace App\Exceptions\Order;

use RuntimeException;

class EmptyCartException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cart is empty.');
    }
}
