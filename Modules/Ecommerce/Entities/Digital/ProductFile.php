<?php

namespace Modules\Ecommerce\Entities\Digital;

use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\Order;
use Modules\Ecommerce\Entities\OrderDetail;

class ProductFile extends Model
{
    protected $table = 'product_files';
    protected $guarded = [];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_current' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function updates()
    {
        return $this->hasMany(ProductUpdate::class, 'file_id');
    }

    public function getFileSizeForHumansAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
