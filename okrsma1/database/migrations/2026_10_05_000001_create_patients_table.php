<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('patients',function(Blueprint $t){$t->id();$t->string('medical_record_no')->nullable()->index();$t->string('name');$t->enum('gender',['P','L']);$t->date('birth_date')->nullable();$t->timestamps();});} public function down():void{Schema::dropIfExists('patients');} };
