<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLowStockNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LowStockDetected $event): void
    {
        // Your notification logic here
        logger()->info("Low stock detected for item {$event->stock->inventoryItem->name}, for the Warehouse {$event->stock->warehouse->name}");
    }
}
