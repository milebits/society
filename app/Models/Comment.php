<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'owner_id', 'owner_type',
        'commentable_id', 'commentable_type',
        'content',
        'attachment_id', 'attachment_type',
    ];

    /**
     * @return MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function attachment()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeWhereOwnerIs(Builder $builder, Model $owner)
    {
        return $builder->where(function (Builder $builder) use ($owner) {
            return $builder
                ->where("owner_id", '=', $owner->getKey())
                ->where("owner_type", '=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeWhereOwnerIsNot(Builder $builder, Model $owner)
    {
        return $builder->where(function (Builder $builder) use ($owner) {
            return $builder
                ->where("owner_id", '!=', $owner->getKey())
                ->where("owner_type", '!=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeOrWhereOwnerIs(Builder $builder, Model $owner)
    {
        return $builder->orWhere(function (Builder $builder) use ($owner) {
            return $builder
                ->where("owner_id", '=', $owner->getKey())
                ->where("owner_type", '=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeOrWhereOwnerIsNot(Builder $builder, Model $owner)
    {
        return $builder->orWhere(function (Builder $builder) use ($owner) {
            return $builder
                ->where("owner_id", '!=', $owner->getKey())
                ->where("owner_type", '!=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $attachment
     * @return Builder
     */
    public function scopeWhereAttachmentIs(Builder $builder, Model $attachment)
    {
        return $builder->where(function (Builder $builder) use ($attachment) {
            return $builder->where("attachment_id", "=", $attachment->getKey())
                ->where("attachment_type", "=", $attachment->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $attachment
     * @return Builder
     */
    public function scopeWhereAttachmentIsNot(Builder $builder, Model $attachment)
    {
        return $builder->where(function (Builder $builder) use ($attachment) {
            return $builder->where("attachment_id", "!=", $attachment->getKey())
                ->where("attachment_type", "!=", $attachment->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $attachment
     * @return Builder
     */
    public function scopeOrWhereAttachmentIs(Builder $builder, Model $attachment)
    {
        return $builder->orWhere(function (Builder $builder) use ($attachment) {
            return $builder->where("attachment_id", "=", $attachment->getKey())
                ->where("attachment_type", "=", $attachment->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $attachment
     * @return Builder
     */
    public function scopeOrWhereAttachmentIsNot(Builder $builder, Model $attachment)
    {
        return $builder->orWhere(function (Builder $builder) use ($attachment) {
            return $builder->where("attachment_id", "!=", $attachment->getKey())
                ->where("attachment_type", "!=", $attachment->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWhereHasAttachment(Builder $builder)
    {
        return $builder->where(function (Builder $builder) {
            return $builder->whereNotNull("attachment_id")->whereNotNull("attachment_type");
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeWhereHasNoAttachment(Builder $builder)
    {
        return $builder->where(function (Builder $builder) {
            return $builder->whereNull("attachment_id")->whereNull("attachment_type");
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrWhereHasAttachment(Builder $builder)
    {
        return $builder->orWhere(function (Builder $builder) {
            return $builder->whereNotNull("attachment_id")->whereNotNull("attachment_type");
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrWhereHasNoAttachment(Builder $builder)
    {
        return $builder->orWhere(function (Builder $builder) {
            return $builder->whereNull("attachment_id")->whereNull("attachment_type");
        });
    }
}
