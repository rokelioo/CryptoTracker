<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->decimal('circulating_supply', 20, 2)->change();  // Increased to 20 digits
            $table->decimal('total_supply', 20, 2)->change();  // Increased to 20 digits
            $table->decimal('max_supply', 20, 2)->change();  // Increased to 20 digits
    
            // Increasing precision for percentage columns
            $table->decimal('percent_change_1h', 10, 4)->change();  // Increased to 10 digits and 4 decimals
            $table->decimal('percent_change_24h', 10, 4)->change();  // Increased to 10 digits and 4 decimals
            $table->decimal('percent_change_7d', 10, 4)->change();  // Increased to 10 digits and 4 decimals
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('crypto_currencies', function (Blueprint $table) {
            $table->decimal('circulating_supply', 14, 2)->change();  // Return to original precision
            $table->decimal('total_supply', 14, 2)->change();  // Return to original precision
            $table->decimal('max_supply', 14, 2)->change();  // Return to original precision
    
            // Returning precision for percentage columns to original
            $table->decimal('percent_change_1h', 5, 2)->change();  // Original precision
            $table->decimal('percent_change_24h', 5, 2)->change();  // Original precision
            $table->decimal('percent_change_7d', 5, 2)->change();  // Original precision
        });
    }
};
