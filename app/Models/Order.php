<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $phone
 * @property string $address
 * @property string $description
 * @property string $delivery_time
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Order extends Model
{
    protected $fillable = [
      'phone',
      'address',
      'delivery_time',
      'description',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot('quantity');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
