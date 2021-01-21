<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Milebits\Society\Models\FriendRequest;

class CreateFriendRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('friend_requests', function (Blueprint $table) {
            $table->id();
            $table->morphs(FriendRequest::RECIPIENT_MORPH);
            $table->morphs(FriendRequest::SENDER_MORPH);
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('friend_requests');
    }
}
