<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('local')) {
            // Update equipamentos table
            try {
                Schema::table('equipamentos', function (Blueprint $table) {
                    $table->dropForeign(['setor_id']);
                });
            } catch (Throwable $e) {
                // FK may not exist or may have different name
            }

            Schema::table('equipamentos', function (Blueprint $table) {
                $table->integer('setor_id')->nullable()->change();
            });

            // Nullify orphan setor_id values in equipamentos that don't exist in local table
            DB::statement('UPDATE equipamentos SET setor_id = NULL WHERE setor_id IS NOT NULL AND setor_id NOT IN (SELECT id_local FROM local)');

            Schema::table('equipamentos', function (Blueprint $table) {
                $table->foreign('setor_id', 'equipamentos_setor_id_foreign')
                    ->references('id_local')
                    ->on('local')
                    ->nullOnDelete();
            });

            // Update perifericos table
            try {
                Schema::table('perifericos', function (Blueprint $table) {
                    $table->dropForeign(['setor_id']);
                });
            } catch (Throwable $e) {
                // FK may not exist or may have different name
            }

            Schema::table('perifericos', function (Blueprint $table) {
                $table->integer('setor_id')->nullable()->change();
            });

            // Nullify orphan setor_id values in perifericos that don't exist in local table
            DB::statement('UPDATE perifericos SET setor_id = NULL WHERE setor_id IS NOT NULL AND setor_id NOT IN (SELECT id_local FROM local)');

            Schema::table('perifericos', function (Blueprint $table) {
                $table->foreign('setor_id', 'perifericos_setor_id_foreign')
                    ->references('id_local')
                    ->on('local')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('local')) {
            try {
                Schema::table('equipamentos', function (Blueprint $table) {
                    $table->dropForeign('equipamentos_setor_id_foreign');
                });
            } catch (Throwable $e) {
            }

            try {
                Schema::table('perifericos', function (Blueprint $table) {
                    $table->dropForeign('perifericos_setor_id_foreign');
                });
            } catch (Throwable $e) {
            }
        }
    }
};
