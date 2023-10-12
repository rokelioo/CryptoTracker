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
        Schema::create('crypto_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('slug');
            $table->integer('rank')->nullable();
            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('volume_24h', 14, 2)->nullable();
            $table->decimal('market_cap', 14, 2)->nullable();
            $table->decimal('circulating_supply', 14, 2)->nullable();
            $table->decimal('total_supply', 14, 2)->nullable();
            $table->decimal('max_supply', 14, 2)->nullable();
            $table->decimal('percent_change_1h', 5, 2)->nullable();
            $table->decimal('percent_change_24h', 5, 2)->nullable();
            $table->decimal('percent_change_7d', 5, 2)->nullable();
            $table->timestamp('last_updated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_currencies');
    }
};
