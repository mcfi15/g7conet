<?php

namespace Modules\Ecommerce\Entities;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;
use Modules\Brand\Entities\Brand;
use Modules\Category\Entities\Category;
use Modules\Wishlist\App\Models\Wishlist;

class Product extends Model
{
    protected $guarded = [];

    protected $appends = ['name', 'description', 'seo_title', 'seo_description', 'is_digital'];

    // Relationship with main Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    // Define the relationship with ProductTranslation
    public function translate()
    {
        return $this->belongsTo(ProductTranslation::class, 'id', 'product_id')->where('lang_code', admin_lang());
    }

    public function front_translate(){
        return $this->belongsTo(ProductTranslation::class, 'id', 'product_id')->where('lang_code', front_lang());
    }

    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }

    public function getNameAttribute()
    {
        return $this->front_translate?->name ?? $this->translate?->name ?? null;
    }

    public function getDescriptionAttribute()
    {
        return $this->front_translate?->description ?? $this->translate?->description ?? null;
    }

    public function getSeoTitleAttribute()
    {
        return $this->front_translate?->seo_title ?? $this->translate?->seo_title ?? null;
    }

    public function getSeoDescriptionAttribute()
    {
        return $this->front_translate?->seo_description ?? $this->translate?->seo_description ?? null;
    }

    public function getIsDigitalAttribute()
    {
        return $this->product_type !== 'physical';
    }

    public function isScript(): bool
    {
        return $this->product_type === 'script';
    }

    public function isEbook(): bool
    {
        return $this->product_type === 'ebook';
    }

    public function isPhysical(): bool
    {
        return $this->product_type === 'physical';
    }

    public function getFinalPriceAttribute()
    {
        if ($this->isScript()) {
            return number_format((float)$this->price, 2, '.', '');
        }
        if ($this->isEbook()) {
            return number_format((float)$this->price, 2, '.', '');
        }
        if ($this->offer_price) {
            $discount = ($this->price * $this->offer_price) / 100;
            $finalPrice = $this->price - $discount;
        } else {
            $finalPrice = $this->price;
        }
        return number_format((float) $finalPrice, 2, '.', '');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function getPriceDisplayAttribute()
    {
        if ($this->isScript()) {
            return currency($this->price);
        }
        if ($this->isEbook()) {
            return currency($this->price);
        }
        if ($this->offer_price) {
            return '<del>' . currency($this->price) . '</del>' . currency($this->final_price);
        }

        return  currency($this->final_price);
    }


    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function files()
    {
        return $this->hasMany(\Modules\Ecommerce\Entities\Digital\ProductFile::class);
    }

    public function currentFile()
    {
        return $this->hasOne(\Modules\Ecommerce\Entities\Digital\ProductFile::class)->where('is_current', true);
    }

    public function licenses()
    {
        return $this->hasMany(\Modules\Ecommerce\Entities\Digital\License::class);
    }

    public function updates()
    {
        return $this->hasMany(\Modules\Ecommerce\Entities\Digital\ProductUpdate::class);
    }

    public function hasActiveUpdates(): bool
    {
        return $this->update_support_months
            && $this->created_at->addMonths($this->update_support_months)->isFuture();
    }

    public function scopeDigital($query)
    {
        return $query->where('product_type', '!=', 'physical');
    }

    public function scopePhysical($query)
    {
        return $query->where('product_type', 'physical');
    }

    public function scopeScripts($query)
    {
        return $query->where('product_type', 'script');
    }

    public function scopeEbooks($query)
    {
        return $query->where('product_type', 'ebook');
    }


}
