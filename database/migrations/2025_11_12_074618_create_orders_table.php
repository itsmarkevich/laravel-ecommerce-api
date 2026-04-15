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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('delivery_type', ['pickup', 'courier'])->default('courier');
            $table->string('address', 200);
            $table->string('description', 200)->nullable();
            $table->time('delivery_time');
            $table->enum('status', [
                'created',
                'paid',
                'in_progress',
                'delivering',
                'completed',
                'cancelled'
            ])->default('created');
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
