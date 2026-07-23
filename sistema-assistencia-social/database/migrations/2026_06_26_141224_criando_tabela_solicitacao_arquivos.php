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
        Schema::create('solicitacao_arquivos', function (Blueprint $table) {
            $table->integer('id_solicitacao_arquivo', true);
            $table->integer('id_solicitacao');
            $table->string('observacao', 512)->nullable()->default(''); // No banco original o padrão é uma string vazia ''
            $table->string('nome_arquivo', 128);
            $table->string('tipo_md5', 255)->nullable();

            $table->timestamp('data_inclusao')->useCurrent()->useCurrentOnUpdate();

            $table->char('tipo', 1)->default('n')->comment('s - sigiloso, n - nao');
            $table->string('q_enviou', 128)->nullable();
            $table->integer('cancelado')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitacao_arquivos');
    }
};
