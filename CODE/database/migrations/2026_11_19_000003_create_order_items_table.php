<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * BCNF: the line total (unit_price x quantity) is NOT stored.
 * product_name and unit_price are copies taken when the order is placed (R-04):
 * they stay the same even if the admin renames the product or changes its price.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // A product that appears in an order cannot be deleted: hide it instead.
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('product_name');
            $table->unsignedBigInteger('unit_price'); // VND
            $table->unsignedInteger('quantity');

            // Each product appears once per order; buying 2 means quantity = 2.
            $table->unique(['order_id', 'product_id']);
        });

        DB::statement('ALTER TABLE order_items ADD CONSTRAINT order_items_quantity_positive
            CHECK (quantity >= 1)');
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
