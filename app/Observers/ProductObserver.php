<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function creating(Product $product): void
    {
        $product->slug = Str::slug($product->name);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updating(Product $product): void
    {
        if ($product->isDirty('name')) {
            $product->slug = Str::slug($product->name);
        }
    }
}
