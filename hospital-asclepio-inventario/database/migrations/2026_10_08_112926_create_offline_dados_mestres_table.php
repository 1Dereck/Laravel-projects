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
        Schema::create('offline_dados_mestres', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // 'secretaria', 'local', 'setor'
            $table->unsignedBigInteger('servidor_id'); // ID original do servidor central
            $table->string('nome');
            $table->unsignedBigInteger('pai_id')->nullable(); // Para relacionamento Secretaria -> Local -> Setor
            $table->json('metadados')->nullable();
            $table->timestamps();

            $table->index(['tipo', 'servidor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_dados_mestres');
    }
};
