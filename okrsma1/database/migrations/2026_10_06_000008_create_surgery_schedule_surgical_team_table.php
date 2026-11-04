<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('surgery_schedule_surgical_team',function(Blueprint $t){$t->foreignId('surgery_schedule_id')->constrained('surgery_schedules')->cascadeOnDelete();$t->foreignId('surgical_team_id')->constrained('surgical_teams')->cascadeOnDelete();$t->primary(['surgery_schedule_id','surgical_team_id']);});} public function down():void{Schema::dropIfExists('surgery_schedule_surgical_team');} };
