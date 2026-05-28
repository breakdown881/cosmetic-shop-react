<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY role ENUM('MANAGER', 'ADMIN', 'STAFF', 'SUPPORT') NOT NULL");
        }

        DB::table('admins')->where('role', 'SUPPORT')->update(['role' => 'ADMIN']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE admins MODIFY role ENUM('MANAGER', 'ADMIN', 'STAFF') NOT NULL");
        }

        foreach (Role::ALLOWED_ROLES as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['name' => $roleName],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('admins')->where('role', 'ADMIN')->update(['role' => 'SUPPORT']);
            DB::statement("ALTER TABLE admins MODIFY role ENUM('MANAGER', 'STAFF', 'SUPPORT') NOT NULL");
        }
    }
};
