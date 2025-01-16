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
        Schema::create('tagables', function (Blueprint $table) {
            $table->foreignUuid('tag_id')
                ->nullable()
                ->references('id')
                ->on('tags')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->uuidMorphs('tagable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagables');
    }
};
