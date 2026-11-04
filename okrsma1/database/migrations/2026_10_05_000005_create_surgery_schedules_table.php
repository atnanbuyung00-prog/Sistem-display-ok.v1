<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surgery_schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $t->foreignId('operator_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('anesthesia_id')->nullable()->constrained()->nullOnDelete();
            $t->date('operation_date')->index();
            $t->time('operation_time');
            $t->string('ok_level')->default('OK 1');
            $t->text('diagnosis')->nullable();
            $t->string('procedure');
            $t->boolean('cito')->default(false);
            $t->string('status')->default('Terjadwal');
            $t->text('notes')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('surgery_schedules');
    }
};
