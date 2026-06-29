<?php

namespace Modules\QRCode\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class QRCode extends Model
{
    protected $guarded = [];

    protected $casts = [
        'scheduled_deletion_at' => 'datetime',
        'gradient_enabled' => 'boolean',
        'frame_margin' => 'integer',
        'frame_font_size' => 'integer',
    ];

    protected $table = 'qr_codes';

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

    public static function generateReference(): string
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'QR-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}
