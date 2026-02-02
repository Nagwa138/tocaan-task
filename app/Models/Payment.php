<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    /** @use HasFactory<PaymentFactory> */

    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'order_id',
        'status',
        'method',
        'amount',
        'gateway_response',
        'gateway_metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'gateway_metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Payment status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESSFUL = 'successful';
    public const STATUS_FAILED = 'failed';

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
