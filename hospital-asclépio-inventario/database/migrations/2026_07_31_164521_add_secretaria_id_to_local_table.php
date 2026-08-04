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
        if (Schema::hasTable('local') && ! Schema::hasColumn('local', 'secretaria_id')) {
            Schema::table('local', function (Blueprint $table) {
                $table->integer('secretaria_id')->nullable()->after('id_local');
                $table->foreign('secretaria_id', 'local_secretaria_id_foreign')
                    ->references('id_secretarias')
                    ->on('secretarias')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('local') && Schema::hasColumn('local', 'secretaria_id')) {
            Schema::table('local', function (Blueprint $table) {
                $table->dropForeign('local_secretaria_id_foreign');
                $table->dropColumn('secretaria_id');
            });
        }
    }
};
