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
        Schema::create('equipamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setores')->cascadeOnDelete();
            $table->enum('tipo', ['notebook', 'desktop']);
            $table->string('serial');
            $table->string('marca_modelo')->nullable();
            $table->boolean('kit_teclado_mouse_locado')->default(false);
            $table->string('responsavel_levantamento')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipamentos');
    }
};
