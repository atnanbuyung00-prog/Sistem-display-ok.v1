<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{if(!Schema::hasTable('greetings'))Schema::create('greetings',function(Blueprint $t){$t->id();$t->string('title',100);$t->text('message');$t->time('start_time');$t->time('end_time');$t->boolean('enabled')->default(true);$t->timestamps();});}public function down():void{Schema::dropIfExists('greetings');}};
