<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One cart per member (found by user_id) or per guest browser (found by session_id).
 * Prices are not stored: they are recalculated every time (R-03).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->unique();
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // Exactly one owner: a member or a guest, never both, never none.
        DB::statement('ALTER TABLE carts ADD CONSTRAINT carts_one_owner
            CHECK ((user_id IS NULL) <> (session_id IS NULL))');
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
