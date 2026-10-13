<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('secretarias')) {
            Schema::create('secretarias', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->integer('id_secretarias')->autoIncrement();
                $table->string('secretaria', 128);
                $table->string('chave_secretaria', 128)->nullable();
                $table->string('nome_extenso', 255);
                $table->string('nome_secretario', 100)->nullable();
                $table->string('funcao', 255)->nullable();
                $table->string('portaria', 50)->default('0');
                $table->string('data_ext_port', 150)->nullable();
                $table->integer('ano_portaria')->default(0);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('secretarias');
    }
};
