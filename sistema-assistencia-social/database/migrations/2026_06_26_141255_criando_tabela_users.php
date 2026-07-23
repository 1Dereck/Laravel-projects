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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_usuario', true);
            $table->string('login', 50);
            $table->string('senha', 100);
            $table->string('permissao', 1);

            $table->timestamp('dt_alteracao')->useCurrent()->useCurrentOnUpdate();

            $table->integer('id_usuario_alteracao')->default(0);
            $table->string('nome_usu', 150)->nullable();
            $table->char('tipo_acesso', 1)->default('n')->comment('s - sigiloso, n - não');
            $table->char('ativo', 1)->nullable()->default('s');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
