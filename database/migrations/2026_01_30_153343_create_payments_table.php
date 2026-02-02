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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique(); // Gateway transaction ID
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending');
            $table->string('method'); // Extensible
            $table->decimal('amount', 10, 2);
            $table->json('gateway_response')->nullable(); // Store gateway-specific response
            $table->json('gateway_metadata')->nullable(); // Additional gateway data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
