<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $permissions = [
            'dashboard.view','schedule.view','schedule.manage','schedule.export',
            'master.manage','shift.manage','shift.export','user.manage','display.view'
        ];
        DB::table('users')->where('username','admin')->update(['permissions'=>json_encode($permissions)]);
    }
    public function down(): void
    {
        DB::table('users')->where('username','admin')->update(['permissions'=>json_encode(['schedule.view','schedule.manage','master.manage','shift.manage','user.manage'])]);
    }
};
