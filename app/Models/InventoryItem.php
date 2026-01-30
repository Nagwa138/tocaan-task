<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'category'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Scope for searching by name or SKU
     */
    public function scopeSearch(Builder $query, string $term = null): Builder
    {
        if (!$term) {
            return $query;
        }

        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('category', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for price range filtering
     */
    public function scopePriceRange(Builder $query, float $min = null, float $max = null): Builder
    {
        if ($min && $max) {
            return $query->whereBetween('price', [$min, $max]);
        }

        if ($min) {
            return $query->where('price', '>=', $min);
        }

        if ($max) {
            return $query->where('price', '<=', $max);
        }

        return $query;
    }

    public function scopeWarehouseSearch(Builder $query, array $params)
    {
        return $query->when($params['warehouse_id'] ?? null, function ($q) use ($params) {
            $q->whereHas('warehouses', function ($q) use ($params) {
                $q->where('warehouses.id', $params['warehouse_id']);
            });
        })
            ->when($params['search'] ?? null, function ($query, $search) {
                $query->search($search);
            })
            ->when($params['min_price'] ?? null, function ($query, $minPrice) {
                $query->priceRange($minPrice, null);
            })
            ->when($params['max_price'] ?? null, function ($query, $maxPrice) {
                $query->priceRange(null, $maxPrice);
            })
            ->with(['warehouses' => function ($query) use ($params) {
                $query->when($params['warehouse_id'] ?? null, function ($q) use ($params) {
                    $q->where('warehouses.id', $params['warehouse_id']);
                })
                    ->select('warehouses.id', 'name', 'location')
                    ->withPivot('quantity', 'low_stock_threshold');
            }]);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'stocks')
            ->withPivot('quantity', 'low_stock_threshold')
            ->withTimestamps();
    }
}
