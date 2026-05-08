<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $delivery_type
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $delivery_region
 * @property string|null $delivery_city
 * @property string|null $delivery_street
 * @property string|null $delivery_house
 * @property string|null $delivery_entrance
 * @property string|null $delivery_apartment
 * @property string|null $delivery_postal_code
 * @property-read \App\Models\OrderProduct|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryApartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryEntrance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryHouse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'delivery_type',
        'delivery_region',
        'delivery_city',
        'delivery_street',
        'delivery_house',
        'delivery_entrance',
        'delivery_apartment',
        'delivery_postal_code',
        'description',
        'status',
    ];

    /**
     * @return BelongsToMany<Product, $this, OrderProduct>
    */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->using(OrderProduct::class)
            ->withPivot('quantity', 'product_name', 'product_price');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
