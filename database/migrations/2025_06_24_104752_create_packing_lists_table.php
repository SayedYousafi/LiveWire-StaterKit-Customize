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
        Schema::create('packing_lists', function (Blueprint $table) {
            $table->id();
            $table->string('item_description')->nullable(); // 商品描述
            $table->integer('item_qty')->nullable();        // 總數量 (PCS)

            $table->string('client1')->nullable();          // CTNS NO 1
            $table->string('client2')->nullable();          // CTNS NO 2

            $table->string('pallet')->nullable();           // Pallet 規格 (e.g., P1, P2, etc.)
            $table->decimal('weight', 8, 2)->nullable();     // G.W. (KG)

            $table->decimal('length', 8, 2)->nullable();     // 長
            $table->decimal('width', 8, 2)->nullable();      // 寬
            $table->decimal('height', 8, 2)->nullable();     // 高

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_lists');
    }
};
