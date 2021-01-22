<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoriesTable extends Migration
{
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->morphs('attachment');
            $table->longText('content')->nullable();
            $table->string('enabled')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stories');
    }
}