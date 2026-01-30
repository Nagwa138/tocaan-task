<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location',
        'description'
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function sourceTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'source_warehouse_id');
    }

    public function destinationTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'destination_warehouse_id');
    }

    public function inventoryItems()
    {
        return $this->belongsToMany(InventoryItem::class, 'stocks')
            ->withPivot('quantity', 'low_stock_threshold')
            ->withTimestamps();
    }
}
