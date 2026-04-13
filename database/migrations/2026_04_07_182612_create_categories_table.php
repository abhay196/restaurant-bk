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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id');
            $table->string('name'); 
            
            // The Slug: Unique per restaurant to avoid conflicts
            $table->string('slug'); 
            
            // Status: Default to true (active)
            $table->boolean('is_active')->default(true); 
            
            $table->string('image')->nullable(); 
            $table->timestamps();

            // Relationships
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            
            // Tip: Add a unique index for the combination of restaurant and slug
            $table->unique(['restaurant_id', 'slug']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
