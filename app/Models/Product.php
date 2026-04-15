<?php

namespace App\Models;

use App\Models\Traits\HasSlugScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $type
 * @property float $price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Product extends Model
{
    use HasSlugScope, HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
        'category_id',
    ];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class)
            ->using(OrderProduct::class)
            ->withPivot('quantity');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
