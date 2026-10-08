<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (User::where('username', 'Diretor')->doesntExist()) {
            $seeder = new DatabaseSeeder;
            $seeder->run();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for initial seed
    }
};
