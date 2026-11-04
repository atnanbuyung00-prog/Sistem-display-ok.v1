<?php
use Illuminate\Database\Migrations\Migration;use Illuminate\Database\Schema\Blueprint;use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::table('shifts',function(Blueprint $t){if(!Schema::hasColumn('shifts','note'))$t->text('note')->nullable();});}public function down():void{Schema::table('shifts',function(Blueprint $t){if(Schema::hasColumn('shifts','note'))$t->dropColumn('note');});}};
