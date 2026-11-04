<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('professions',function(Blueprint $t){$t->id();$t->string('name',100)->unique();$t->boolean('active')->default(true);$t->timestamps();}); foreach(['Perawat','Bidan'] as $name)
            \App\Models\Profession::create(['name'=>$name,'active'=>true]);} public function down():void{Schema::dropIfExists('professions');} };
