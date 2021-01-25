<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->morphs("sender");
            $table->morphs("recipient");
            $table->longText("content")->default("");
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp("seen_at")->nullable();
            $table->timestamp("delivered_at")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}