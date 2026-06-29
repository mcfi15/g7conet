<?php

namespace Modules\Invoice\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items' => 'array',
        'scheduled_deletion_at' => 'datetime',
    ];

    protected $appends = ['currency_icon'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('scheduled_deletion_at')
            ->where('scheduled_deletion_at', '<=', now());
    }

    public function isOwnedBy(?User $user): bool
    {
        return $user && $this->user_id === $user->id;
    }

    public function getCurrencyIconAttribute(): string
    {
        $currencies = [
            'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥',
            'CNY' => '¥', 'INR' => '₹', 'BRL' => 'R$', 'CAD' => 'C$',
            'AUD' => 'A$', 'CHF' => 'Fr', 'SEK' => 'kr', 'NOK' => 'kr',
            'DKK' => 'kr', 'PLN' => 'zł', 'TRY' => '₺', 'MXN' => 'MX$',
            'KRW' => '₩', 'NGN' => '₦', 'BDT' => '৳', 'PKR' => '₨',
            'LKR' => 'Rs', 'NPR' => 'Rs', 'MYR' => 'RM', 'PHP' => '₱',
            'SGD' => 'S$', 'HKD' => 'HK$', 'THB' => '฿', 'IDR' => 'Rp',
            'VND' => '₫', 'RUB' => '₽', 'ZAR' => 'R', 'KES' => 'KSh',
        ];
        return $currencies[$this->currency] ?? ($this->currency ?? '$');
    }

    public static function generateInvoiceNumber(): string
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
