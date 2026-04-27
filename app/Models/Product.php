<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'category_id',
        'in_stock',
        'rating',
    ];

    protected function casts(): array
    {
        return [
            'in_stock' => 'boolean',
            'price' => 'decimal:2',
            'rating' => 'float',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param array<string, mixed> $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['q'])) {
            $like = '%' . addcslashes($filters['q'], '%_\\') . '%';
            $query->where('name', 'like', $like);
        }

        if (isset($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }

        if (isset($filters['price_to'])) {
            $query->where('price', '<=', $filters['price_to']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (array_key_exists('in_stock', $filters) && $filters['in_stock'] !== null) {
            $query->where('in_stock', $filters['in_stock']);
        }

        if (isset($filters['rating_from'])) {
            $query->where('rating', '>=', $filters['rating_from']);
        }

        return $query;
    }

    public function scopeSort(Builder $query, string $sort): Builder
    {
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc')
                    ->orderBy('id', 'asc');
                break;

            case 'price_desc':
                $query->orderBy('price', 'desc')
                    ->orderBy('id', 'desc');
                break;

            case 'rating_desc':
                $query->orderBy('rating', 'desc')
                    ->orderBy('id', 'desc');
                break;

            case 'newest':
            default:
                $query->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc');
                break;
        }

        return $query;
    }
}
