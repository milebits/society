<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->boolean("enabled")->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create("taggables", function (Blueprint $table) {
            $table->foreignId("tag_id")->constrained();
            $table->unsignedBigInteger("taggable_id");
            $table->string("taggable_type");
            $table->index(['taggable_id', 'taggable_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
