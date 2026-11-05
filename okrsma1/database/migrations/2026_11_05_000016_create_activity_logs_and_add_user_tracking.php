<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create activity_logs table for IT audit trail
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('user_username')->nullable();
            $table->string('action', 50)->index(); // INPUT, UBAH, HAPUS, SALIN, RESTORE, BACKUP
            $table->string('module', 100)->index(); // Jadwal Operasi, Pasien, Shift, User, Data Master, Database
            $table->unsignedBigInteger('record_id')->nullable()->index();
            $table->string('patient_name')->nullable()->index();
            $table->date('operation_date')->nullable()->index();
            $table->text('description');
            $table->longText('old_values')->nullable();
            $table->longText('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // 2. Add creator and updater tracking to surgery_schedules
        Schema::table('surgery_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable()->after('notes');
            $table->unsignedBigInteger('updated_by_user_id')->nullable()->after('created_by_user_id');

            $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surgery_schedules', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropForeign(['updated_by_user_id']);
            $table->dropColumn(['created_by_user_id', 'updated_by_user_id']);
        });

        Schema::dropIfExists('activity_logs');
    }
};
