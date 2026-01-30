<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_warehouse_id',
        'destination_warehouse_id',
        'inventory_item_id',
        'user_id',
        'quantity',
        'status',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime:d-m-Y H:i:s',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];

    public function sourceWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
