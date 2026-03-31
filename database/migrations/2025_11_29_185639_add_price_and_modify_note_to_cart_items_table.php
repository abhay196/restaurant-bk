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
        Schema::table('cart_items', function (Blueprint $table) {
            // 1. Add 'price' column
            // DECIMAL(8, 2) is suitable for currency: up to 999,999.99
            $table->decimal('price', 8, 2)->default(0.00)->after('qty');
            
            // 2. Modify 'note' column to explicitly set default NULL
            // Use change() to modify the column attributes
            $table->string('note', 255)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // 1. Drop the added 'price' column
            $table->dropColumn('price');

            // 2. Revert the 'note' column modification
            // Revert default to what it was (typically no explicit default is needed for nullable Varchar)
            // You must still use the change() method to undo a change.
            $table->string('note', 255)->nullable()->default(null)->change();
        });
    }
};
