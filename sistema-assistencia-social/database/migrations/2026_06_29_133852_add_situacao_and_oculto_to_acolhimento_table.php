<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acolhimento', function (Blueprint $table) {
            $table->string('situacao', 30)->default('Ativo')->after('nome_social');
            $table->char('oculto', 1)->default('n')->after('situacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acolhimento', function (Blueprint $table) {
            $table->dropColumn(['situacao', 'oculto']);
        });
    }
};
