<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 */

class OrderProduct extends Pivot
{
    public $timestamps = false;
    public $incrementing = true;
    protected $fillable = ['quantity'];
}
