<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('roles')) {
            return;
        }

        $admin = DB::table('roles')->where('slug', 'admin')->first();
        $principal = DB::table('roles')->where('slug', 'principal')->first();

        // If both admin and principal exist, move users from admin to principal then delete admin
        if ($admin && $principal) {
            DB::table('users')->where('role_id', $admin->id)->update(['role_id' => $principal->id]);
            DB::table('roles')->where('id', $admin->id)->delete();
            return;
        }

        // If admin exists but principal does not, rename admin to principal
        if ($admin && !$principal) {
            DB::table('roles')->where('id', $admin->id)->update([
                'slug' => 'principal',
                'name' => 'Principal',
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible safely in all setups; leave intentionally empty.
    }
};
