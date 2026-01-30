<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'inventory_item_id',
        'quantity',
        'low_stock_threshold'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'created_at' => 'datetime:d-m-Y H:i:s',
        'updated_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }
}
