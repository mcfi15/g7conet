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

    /**
     * Master list of supported currencies: code => symbol.
     * This is the single source of truth for the whole Invoice module.
     */
    public static function currencyList(): array
    {
        return [
            'AED' => 'د.إ', 'AFN' => '؋', 'ALL' => 'L', 'AMD' => '֏',
            'ANG' => 'ƒ', 'AOA' => 'Kz', 'ARS' => '$', 'AUD' => 'A$',
            'AWG' => 'ƒ', 'AZN' => '₼', 'BAM' => 'KM', 'BBD' => 'Bds$',
            'BDT' => '৳', 'BGN' => 'лв', 'BHD' => '.د.ب', 'BIF' => 'FBu',
            'BMD' => 'BD$', 'BND' => 'B$', 'BOB' => 'Bs.', 'BRL' => 'R$',
            'BSD' => 'B$', 'BTN' => 'Nu.', 'BWP' => 'P', 'BYN' => 'Br',
            'BZD' => 'BZ$', 'CAD' => 'C$', 'CDF' => 'FC', 'CHF' => 'Fr',
            'CLP' => '$', 'CNY' => '¥', 'COP' => '$', 'CRC' => '₡',
            'CUP' => '$', 'CVE' => '$', 'CZK' => 'Kč', 'DJF' => 'Fdj',
            'DKK' => 'kr', 'DOP' => 'RD$', 'DZD' => 'د.ج', 'EGP' => '£',
            'ERN' => 'Nfk', 'ETB' => 'Br', 'EUR' => '€', 'FJD' => 'FJ$',
            'FKP' => '£', 'GBP' => '£', 'GEL' => '₾', 'GHS' => '₵',
            'GIP' => '£', 'GMD' => 'D', 'GNF' => 'FG', 'GTQ' => 'Q',
            'GYD' => 'G$', 'HKD' => 'HK$', 'HNL' => 'L', 'HTG' => 'G',
            'HUF' => 'Ft', 'IDR' => 'Rp', 'ILS' => '₪', 'INR' => '₹',
            'IQD' => 'ع.د', 'IRR' => '﷼', 'ISK' => 'kr', 'JMD' => 'J$',
            'JOD' => 'د.ا', 'JPY' => '¥', 'KES' => 'KSh', 'KGS' => 'с',
            'KHR' => '៛', 'KMF' => 'CF', 'KPW' => '₩', 'KRW' => '₩',
            'KWD' => 'د.ك', 'KYD' => 'CI$', 'KZT' => '₸', 'LAK' => '₭',
            'LBP' => 'ل.ل', 'LKR' => 'Rs', 'LRD' => 'L$', 'LSL' => 'L',
            'LYD' => 'ل.د', 'MAD' => 'د.م.', 'MDL' => 'L', 'MGA' => 'Ar',
            'MKD' => 'ден', 'MMK' => 'K', 'MNT' => '₮', 'MOP' => 'MOP$',
            'MRU' => 'UM', 'MUR' => '₨', 'MVR' => 'Rf', 'MWK' => 'MK',
            'MXN' => 'MX$', 'MYR' => 'RM', 'MZN' => 'MT', 'NAD' => 'N$',
            'NGN' => '₦', 'NIO' => 'C$', 'NOK' => 'kr', 'NPR' => 'Rs',
            'NZD' => 'NZ$', 'OMR' => 'ر.ع.', 'PAB' => 'B/.', 'PEN' => 'S/',
            'PGK' => 'K', 'PHP' => '₱', 'PKR' => '₨', 'PLN' => 'zł',
            'PYG' => '₲', 'QAR' => 'ر.ق', 'RON' => 'lei', 'RSD' => 'дин',
            'RUB' => '₽', 'RWF' => 'FRw', 'SAR' => 'ر.س', 'SBD' => 'SI$',
            'SCR' => '₨', 'SDG' => 'ج.س.', 'SEK' => 'kr', 'SGD' => 'S$',
            'SHP' => '£', 'SLE' => 'Le', 'SOS' => 'Sh', 'SRD' => '$',
            'SSP' => '£', 'STN' => 'Db', 'SYP' => '£', 'SZL' => 'L',
            'THB' => '฿', 'TJS' => 'ЅМ', 'TMT' => 'm', 'TND' => 'د.ت',
            'TOP' => 'T$', 'TRY' => '₺', 'TTD' => 'TT$', 'TWD' => 'NT$',
            'TZS' => 'TSh', 'UAH' => '₴', 'UGX' => 'USh', 'USD' => '$',
            'UYU' => '$U', 'UZS' => 'soʻm', 'VES' => 'Bs.', 'VND' => '₫',
            'VUV' => 'VT', 'WST' => 'WS$', 'XAF' => 'FCFA', 'XCD' => 'EC$',
            'XOF' => 'CFA', 'XPF' => '₣', 'YER' => '﷼', 'ZAR' => 'R',
            'ZMW' => 'ZK', 'ZWL' => 'Z$',
        ];
    }

    public function getCurrencyIconAttribute(): string
    {
        return self::currencyList()[$this->currency] ?? '$';
    }

    public static function generateInvoiceNumber(): string
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'INV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}