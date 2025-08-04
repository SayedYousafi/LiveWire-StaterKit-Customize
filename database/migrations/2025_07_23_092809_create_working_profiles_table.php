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
        Schema::create('working_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('location')->nullable();
            $table->decimal('count_leave')->nullable();
            $table->tinyInteger('Monday')->nullable();
            $table->tinyInteger('Tuesday')->nullable();
            $table->tinyInteger('Wednsday')->nullable();
            $table->tinyInteger('Thursday')->nullable();
            $table->tinyInteger('Friday')->nullable();
            $table->tinyInteger('Saturday')->nullable();
            $table->tinyInteger('Sunday')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('working_profiles');
    }
};
