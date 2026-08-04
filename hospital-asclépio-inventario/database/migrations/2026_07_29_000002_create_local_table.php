<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('local')) {
            Schema::create('local', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->integer('id_local')->autoIncrement();
                $table->string('local', 150)->nullable();
                $table->string('telefone', 150)->nullable()->default('');
                $table->string('bairro', 150)->nullable()->default('');
                $table->string('rua', 150)->nullable()->default('');
                $table->integer('numero')->nullable()->default(0);
                $table->string('cep', 20)->nullable()->default('');
                $table->string('latitude', 30)->nullable()->default('');
                $table->string('longitude', 30)->nullable()->default('');
                $table->string('status', 25)->nullable();
                $table->timestamp('ultima_atualizacao')->nullable();
                $table->string('ip_onu', 20)->nullable();
                $table->char('tipo_local', 1)->nullable();
                $table->char('flag_situacao', 1)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('local');
    }
};
