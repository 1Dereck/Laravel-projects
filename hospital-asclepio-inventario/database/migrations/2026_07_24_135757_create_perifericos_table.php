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
        Schema::create('perifericos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setores')->cascadeOnDelete();
            $table->foreignId('equipamento_id')->nullable()->constrained('equipamentos')->nullOnDelete();
            $table->string('tipo');
            $table->string('serial_patrimonio')->nullable();
            $table->text('observacoes')->nullable();
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
        Schema::dropIfExists('perifericos');
    }
};
