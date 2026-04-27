<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['address', 'delivery_time']);

            $table->string('delivery_region', 100)->nullable();
            $table->string('delivery_city', 100)->nullable();
            $table->string('delivery_street', 150)->nullable();
            $table->string('delivery_house', 30)->nullable();
            $table->string('delivery_entrance', 30)->nullable();
            $table->string('delivery_apartment', 30)->nullable();
            $table->string('delivery_postal_code', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address', 200);
            $table->time('delivery_time');

            $table->dropColumn([
                'delivery_region',
                'delivery_city',
                'delivery_street',
                'delivery_house',
                'delivery_entrance',
                'delivery_apartment',
                'delivery_postal_code',
            ]);
        });
    }
};
