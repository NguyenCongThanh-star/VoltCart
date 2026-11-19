<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * BCNF: no column here can be calculated from other columns.
 * Subtotal and total are NOT stored; App\Models\Order works them out from the items.
 * Discount and shipping fee ARE stored, because the coupon or the fee may change later
 * and the order must keep what the customer actually paid.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // VC-YYYYMMDD-XXXX

            // Null for guest orders (R-20). Accounts are locked, not deleted.
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            // Null when no coupon was used. Used coupons are switched off, not deleted.
            $table->foreignId('coupon_id')->nullable()->constrained()->restrictOnDelete();

            // Delivery details typed at checkout. They can differ from the account profile.
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20);
            $table->string('shipping_address');

            $table->string('payment_method', 20);                // cod | bank_transfer
            $table->unsignedBigInteger('discount')->default(0);  // VND, copied when the order is placed
            $table->unsignedBigInteger('shipping_fee');          // VND, copied when the order is placed (R-14)
            $table->string('status', 20)->default('pending')->index(); // R-16
            $table->timestamps();

            $table->index('created_at'); // revenue report by date
        });

        // No coupon means no discount.
        DB::statement('ALTER TABLE orders ADD CONSTRAINT orders_discount_needs_coupon
            CHECK (coupon_id IS NOT NULL OR discount = 0)');
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_allowed
            CHECK (status IN ('pending', 'confirmed', 'shipping', 'completed', 'cancelled'))");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_payment_allowed
            CHECK (payment_method IN ('cod', 'bank_transfer'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
