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
        Schema::table('short_descriptions', function (Blueprint $table) {
            $table->string('parent_name_en')->after('parent_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('short_descriptions', function (Blueprint $table) {
            $table->dropColumn(['parent_name_en']);
        });
    }
};
