<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeansTable extends Migration
{
    public function up()
    {
        Schema::create('leans', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->morphs('leanable');
            $table->string('status')->default("like");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leans');
    }
}
