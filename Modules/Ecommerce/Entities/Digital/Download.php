<?php

namespace Modules\Ecommerce\Entities\Digital;

use Illuminate\Database\Eloquent\Model;
use Modules\Ecommerce\Entities\Product;
use Modules\Ecommerce\Entities\Order;

class Download extends Model
{
    protected $table = 'downloads';
    public $timestamps = false;
    protected $guarded = [];

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
}
