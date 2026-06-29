<?php

namespace Modules\Ecommerce\Entities\Digital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\Order;

class License extends Model
{
    protected $table = 'licenses';
    protected $guarded = [];

    protected $casts = [
        'activated_domains' => 'array',
        'activation_limit' => 'integer',
        'activations_count' => 'integer',
        'expires_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    const TYPE_REGULAR = 'regular';
    const TYPE_EXTENDED = 'extended';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public static function generateUniqueKey(): string
    {
        do {
            $key = strtoupper(implode('-', [
                Str::random(8), Str::random(4), Str::random(4),
                Str::random(4), Str::random(12),
            ]));
        } while (static::where('license_key', $key)->exists());

        return $key;
    }

    public function canActivate(): bool
    {
        return !$this->is_locked
            && $this->activations_count < $this->activation_limit
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
