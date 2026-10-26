<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150)->index();
            $table->string('category', 80)->index();
            $table->string('unit', 20);
            $table->decimal('buy_price', 12, 2)->default(0);
            $table->decimal('sell_price', 12, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(5);
            $table->timestamps();
        });

        // Stok tidak boleh negatif (MySQL 8.0.16+)
        DB::statement('ALTER TABLE items ADD CONSTRAINT chk_items_stock CHECK (stock >= 0)');
        DB::statement('ALTER TABLE items ADD CONSTRAINT chk_items_min_stock CHECK (min_stock >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
