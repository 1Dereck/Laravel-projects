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

        Schema::create('observacao', function (Blueprint $table) {
            $table->integer('id_observacao', true);
            $table->integer('id_acolhimento')->default(0);
            $table->text('descricao')->nullable();
            $table->integer('id_assunto')->default(0);

            $table->timestamp('ultima_data')->useCurrent()->useCurrentOnUpdate();

            $table->char('tipo', 1)->default('n')->comment('s - sigiloso, n - não');
            $table->integer('id_usuario')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observacao');
    }
};
