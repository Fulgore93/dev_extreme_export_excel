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
        Schema::create('items_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('accion_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('descripcion');
            $table->tinyInteger('estado');
            $table->timestamps();
            
            $table->foreign('accion_id')->references('id')->on('items_acciones');
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_detalles');
    }
};
