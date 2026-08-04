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
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('diretor', 'administrador', 'usuario') NOT NULL DEFAULT 'administrador'");

            if (! Schema::hasColumn('users', 'setor_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('setor_id')->nullable()->after('role');
                });
            } else {
                DB::statement('ALTER TABLE `users` MODIFY COLUMN `setor_id` INT NULL');
            }
        } else {
            if (! Schema::hasColumn('users', 'setor_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('setor_id')->nullable()->after('role');
                });
            }
        }

        if (Schema::hasTable('local')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('setor_id', 'users_setor_id_foreign')
                        ->references('id_local')
                        ->on('local')
                        ->nullOnDelete();
                });
            } catch (Throwable $e) {
                // FK já existente ou incompatível na suíte de testes
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('users_setor_id_foreign');
                $table->dropColumn('setor_id');
            });

            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('diretor', 'administrador') NOT NULL DEFAULT 'administrador'");
        } else {
            if (Schema::hasColumn('users', 'setor_id')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('setor_id');
                });
            }
        }
    }
};
