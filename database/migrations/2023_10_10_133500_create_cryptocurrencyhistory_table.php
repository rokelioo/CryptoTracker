<?php

use App\Models\CryptoCurrency;
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
        Schema::create('crypto_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CryptoCurrency::class);
            $table->decimal('price', 14, 2)->nullable();
            $table->decimal('volume_24h', 14, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_histories');
    }
};
