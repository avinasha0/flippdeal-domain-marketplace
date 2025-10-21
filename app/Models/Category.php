<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the domains in this category.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'category', 'name');
    }

    /**
     * Get the active domains in this category.
     */
    public function activeDomains(): HasMany
    {
        return $this->domains()->active();
    }

    /**
     * Get the domain count for this category.
     */
    public function getDomainCountAttribute(): int
    {
        return $this->activeDomains()->count();
    }

    /**
     * Generate a unique slug for the category.
     */
    public function generateSlug(): string
    {
        $slug = Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order categories by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Boot method to automatically generate slug before saving.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateSlug();
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = $category->generateSlug();
            }
        });
    }
}