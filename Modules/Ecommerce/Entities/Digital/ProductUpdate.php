<?php

namespace Modules\Ecommerce\Entities\Digital;

use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Entities\Product;

class ProductUpdate extends Model
{
    protected $table = 'product_updates';
    protected $guarded = [];

    protected $casts = [
        'released_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function file()
    {
        return $this->belongsTo(ProductFile::class, 'file_id');
    }
}
