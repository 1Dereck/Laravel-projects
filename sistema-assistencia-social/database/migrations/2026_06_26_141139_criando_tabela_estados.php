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
        Schema::create('estados', function (Blueprint $table) {
            $table->integer('id_estados', true);

            $sigla = $table->string('sigla', 2)->nullable();
            $nome = $table->string('nome', 72)->nullable();

            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $sigla->collation('utf8_unicode_ci');
                $nome->collation('utf8_unicode_ci');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estados');
    }
};
