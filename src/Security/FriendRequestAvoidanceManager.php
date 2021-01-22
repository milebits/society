<?php


namespace Milebits\Society\Security;

use Illuminate\Database\Eloquent\Model;
use Milebits\Society\Models\FriendRequest;

/**
 * Trait FriendRequestAvoidanceManager
 * @package Milebits\Society\Security
 * @mixin Model
 */
trait FriendRequestAvoidanceManager
{
    public static function bootSameSenderAndRecipientAvoidance()
    {
        self::creating(function ($model): bool {
            if (self::senderIsRecipient($model)) return false;
            return true;
        });
        self::updating(function ($model): bool {
            if (self::senderIsRecipient($model)) return false;
            return true;
        });
    }

    /**
     * @param string $attribute
     * @return Model
     */
    public function morphModel(string $attribute): Model
    {
        return $this->{sprintf("%s_type", $attribute)}::find($this->{sprintf("%s_id", $attribute)});
    }

    /**
     * @param $model
     * @return bool
     */
    public static function senderIsRecipient($model): bool
    {
        return $model->morphModel(FriendRequest::SENDER_MORPH)->is($model->morphModel(FriendRequest::RECIPIENT_MORPH));
    }
}