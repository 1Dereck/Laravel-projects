<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('diretor', 'administrador', 'coordenador', 'usuario') NOT NULL DEFAULT 'administrador'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('diretor', 'administrador', 'usuario') NOT NULL DEFAULT 'administrador'");
        }
    }
};
