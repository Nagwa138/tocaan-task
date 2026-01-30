<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('destination_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->string('status')->default('completed'); // or 'pending', 'cancelled'
            $table->text('notes')->nullable();
            $table->index(['inventory_item_id', 'quantity']); // For item availability
            $table->index('source_warehouse_id'); // For transfer history
            $table->index('destination_warehouse_id'); // For transfer history
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
