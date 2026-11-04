<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shift_assignments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $t->string('staff_name');
            $t->string('staff_role')->default('Dinas');
            $t->date('shift_date');
            $t->timestamps();
            $t->index(['shift_date', 'shift_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('shift_assignments');
    }
};
