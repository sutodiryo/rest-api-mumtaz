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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('quantity');
            $table->decimal('total_amount', 14, 2);
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->foreignUuid('product_id')
                ->nullable()
                ->references('id')
                ->on('products')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignUuid('transaction_id')
                ->nullable()
                ->references('id')
                ->on('transactions')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignUuid('created_by_id')
                ->nullable()
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
