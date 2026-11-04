<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('shifts',function(Blueprint $t){$t->id();$t->string('name');$t->time('start_time');$t->time('end_time');$t->string('color')->default('#0d6efd');$t->boolean('active')->default(true);$t->timestamps();});} public function down():void{Schema::dropIfExists('shifts');} };
