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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->integer('order_type_id')->after('id')->nullable();
            $table->string('company_name')->after('name')->nullable();
            $table->string('extra_note')->after('company_name')->nullable();
            $table->tinyInteger('min_order_value')->after('extra_note')->nullable();
            $table->tinyInteger('is_fully_prepared')->after('min_order_value')->nullable();
            $table->tinyInteger('is_tax_included')->after('is_fully_prepared')->nullable();
            $table->tinyInteger('is_freight_included')->after('is_tax_included')->nullable();
            $table->string('province')->after('is_freight_included')->nullable();
            $table->string('city')->after('province')->nullable();
            $table->string('street')->after('city')->nullable();
            $table->string('full_address')->after('street')->nullable();
            $table->string('contact_person')->after('full_address')->nullable();
            $table->string('phone')->after('contact_person')->nullable();
            $table->string('mobile')->after('phone')->nullable();
            $table->string('email')->after('mobile')->nullable();
            $table->string('website')->after('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['order_type_id']);
        });
    }
};
