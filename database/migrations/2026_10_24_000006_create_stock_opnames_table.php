<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->date('date')->index();
            $table->integer('system_stock');
            $table->integer('physical_stock');
            $table->integer('difference');
            $table->string('note', 255)->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE stock_opnames ADD CONSTRAINT chk_opnames_physical CHECK (physical_stock >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
