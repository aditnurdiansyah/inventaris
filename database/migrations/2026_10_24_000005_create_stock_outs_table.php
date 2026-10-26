<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date')->index();
            $table->integer('quantity');
            $table->enum('reason', ['terjual', 'rusak', 'kedaluwarsa', 'lainnya'])->default('terjual');
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE stock_outs ADD CONSTRAINT chk_stock_outs_quantity CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_outs');
    }
};
