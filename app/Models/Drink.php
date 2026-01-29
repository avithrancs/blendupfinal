<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category',
        'image_url',
        'is_featured',
        'description',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getBadgeAttribute(): array
    {
        $name = strtolower($this->name);
        $cat  = strtolower($this->category);

        if ($this->is_featured)             return ['Popular','bg-olive-green'];
        if (str_contains($name,'shot'))     return ['Shot','bg-deep-teal'];
        if (str_contains($name,'refresh'))  return ['Refreshing','bg-olive-green'];
        if ($cat === 'seasonal')            return ['New','bg-muted-coral'];
        if (str_contains($name,'detox') || str_contains($name,'green')) return ['Detox','bg-olive-green'];
        if (str_contains($name,'orange'))   return ['Classic','bg-deep-teal'];
        
        return ['Bestseller','bg-deep-teal'];
    }

    public function getShortDescriptionAttribute(): string
    {
        if ($this->description) return $this->description;

        $name = strtolower($this->name);
        if (str_contains($name,'green'))    return 'Spinach, banana, apple, ginger & coconut water. Packed with vitamins & minerals.';
        if (str_contains($name,'tropical')) return 'Mango, pineapple, passion fruit & coconut milk. A tropical escape in every sip.';
        if (str_contains($name,'orange'))   return '100% pure fresh orange juice. No additives—just vitamin C goodness.';
        if (str_contains($name,'carrot'))   return 'Fresh carrot and ginger. Perfect immunity booster.';
        if (str_contains($name,'berry'))    return 'Mixed berries blended smooth. Antioxidant rich and delicious.';
        if (str_contains($name,'citrus'))   return 'Orange, lemon & lime. Bright, zesty, super refreshing.';
        
        return 'Naturally sweet, blended fresh to order. Great taste, great energy.';
    }
}
