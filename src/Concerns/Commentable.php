<?php


namespace Milebits\Society\Concerns;

use Milebits\Society\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Commentable
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait Commentable
{
    /**
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @param Model $owner
     * @param string $content
     * @param Model|null $attachment
     * @return Model|Comment
     */
    public function addComment(Model $owner, string $content, ?Model $attachment = null)
    {
        return $this->comments()->create([
            'owner_id' => $owner->getKey(),
            'owner_type' => $owner->getMorphClass(),
            'content' => $content,
            'attachment_id' => !is_null($attachment) ? $attachment->getKey() : null,
            'attachment_type' => !is_null($attachment) ? $attachment->getMorphClass() : null,
        ]);
    }
}
