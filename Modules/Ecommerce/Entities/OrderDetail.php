<?php

namespace Modules\Ecommerce\Entities;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function singleProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function license()
    {
        return $this->belongsTo(\Modules\Ecommerce\Entities\Digital\License::class);
    }

    public function downloads()
    {
        return $this->hasMany(\Modules\Ecommerce\Entities\Digital\Download::class, 'order_detail_id');
    }
}
