<?php


namespace Milebits\Society\Security;

use Illuminate\Database\Eloquent\Model;
use Milebits\Society\Models\FriendRequest;

/**
 * Trait SameSenderAndRecipientAvoidance
 * @package Milebits\Society\Security
 * @mixin Model
 */
trait SameSenderAndRecipientAvoidance
{
    public static function bootSameSenderAndRecipientAvoidance()
    {
        self::creating(fn($model) => self::friendRequestAvoidance($model));
        self::updating(fn($model) => self::friendRequestAvoidance($model));
    }

    public static function friendRequestAvoidance(Model $model): bool
    {
        if (!($model instanceof FriendRequest)) return true;
        if (!self::sameModelAvoidance($model)) return false;
        return true;
    }

    /**
     * @param string $attribute
     * @return mixed
     */
    public function morphModel(string $attribute)
    {
        return $this->{sprintf("%s_type", $attribute)}::find($this->{sprintf("%s_id", $attribute)});
    }

    /**
     * @param $model
     * @return mixed
     */
    public static function sameModelAvoidance($model)
    {
        return !$model->morphModel(FriendRequest::SENDER_MORPH)->is($model->morphModel(FriendRequest::RECIPIENT_MORPH));
    }
}