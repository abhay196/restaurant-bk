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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('phone', 20)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['veg', 'non_veg', 'both']);
            $table->boolean('is_available')->default(true)->index();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
