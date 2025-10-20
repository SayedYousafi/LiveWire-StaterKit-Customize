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
        Schema::create('pos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('desc')->nullable();
            $table->text('comment1')->nullable();
            $table->text('comment2')->nullable();
            $table->text('comment3')->nullable();
            $table->text('comment4')->nullable();
            $table->text('comment5')->nullable();
            $table->text('comment6')->nullable();
            $table->text('comment7')->nullable();
            $table->text('comment8')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos');
    }
};
