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
        Schema::create('acolhimento', function (Blueprint $table) {
            // Mantém o tipo INT(11) antigo do MySQL para a chave primária
            $table->integer('id_acolhimento', true);

            // Configuração exata das datas e inteiros com os valores padrão do banco
            $table->date('dt_cadastro')->default('1900-01-01');
            $table->integer('id_tecnico_resp')->default(0);
            $table->date('dt_nascimento')->default('1900-01-01');

            // Strings com os tamanhos exatos do MySQL e permitindo valores vazios (->nullable())
            $table->string('nome_pessoa', 100)->nullable();
            $table->string('naturalidade', 50)->nullable();
            $table->char('estado_nasc', 2)->nullable();

            $table->char('nec_especial', 3)->nullable();
            $table->string('tipo_nec_especial', 100)->nullable();
            $table->char('depend_quimica', 3)->nullable();
            $table->string('tipo_dep_quimica', 100)->nullable();
            $table->char('transtorno', 3)->nullable();
            $table->string('tipo_transtorno', 100)->nullable();

            $table->string('cid_bairro_situacao', 200)->nullable();
            $table->string('pai', 100)->nullable();
            $table->string('mae', 100)->nullable();

            $table->string('parente_grau', 50)->nullable();
            $table->string('parente_end', 150)->nullable();
            $table->string('parente_nome', 100)->nullable();
            $table->string('parente_grau1', 50)->nullable();
            $table->string('parente_end1', 150)->nullable();

            $table->char('monitoramento', 3)->nullable();
            $table->text('obs_pessoa')->nullable();

            // Força o MySQL a atualizar o campo dt_alteracao automaticamente a cada update
            $table->timestamp('dt_alteracao')->nullable(false)->useCurrent()->useCurrentOnUpdate();
            $table->integer('id_usuario_alteracao')->default(0);

            $table->string('nome_foto', 30)->nullable();
            $table->string('nome_cript', 100)->nullable();
            $table->text('hist_monitoramento')->nullable();
            $table->string('rg', 100)->nullable();
            $table->string('cpf', 14)->nullable();

            $table->char('recebe_beneficio', 3)->nullable();
            $table->string('tipo_beneficio', 150)->nullable();
            $table->string('nome_social', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acolhimento');
    }
};
