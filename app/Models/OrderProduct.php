<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property string $product_name
 * @property string|float $product_price
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderProduct query()
 * @mixin \Eloquent
 */
class OrderProduct extends Pivot
{
    public $timestamps = false;
    public $incrementing = true;
    protected $fillable = [
        'quantity',
        'product_name',
        'product_price',
    ];
}
