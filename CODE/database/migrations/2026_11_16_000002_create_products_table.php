<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // A category that still has products cannot be deleted.
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand', 60)->index();

            // Key specifications used by the filters. Nullable because accessories have none.
            $table->unsignedSmallInteger('ram_gb')->nullable()->index();
            $table->unsignedSmallInteger('storage_gb')->nullable();
            $table->decimal('screen_size', 3, 1)->nullable(); // inches

            // Prices in VND (no decimals). Rule: member_price <= guest_price.
            $table->unsignedBigInteger('guest_price')->index();
            $table->unsignedBigInteger('member_price');

            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->text('specifications')->nullable(); // "Label: value" lines shown as a table
            $table->boolean('is_featured')->default(false);
            $table->string('status', 20)->default('active')->index(); // active | hidden
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
