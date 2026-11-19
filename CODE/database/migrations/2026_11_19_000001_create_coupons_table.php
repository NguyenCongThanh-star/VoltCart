<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();            // e.g. VOLT200K
            $table->unsignedBigInteger('discount_amount');   // VND taken off the subtotal
            $table->unsignedBigInteger('min_order_amount')->default(0); // R-12
            // A coupon that was used in an order cannot be deleted: switch it off instead.
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
