<?php


namespace Milebits\Society\Concerns;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait CanDoComments
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait CanDoComments
{
    /**
     * @return MorphToMany
     */
    public function comments()
    {
        return $this->morphedByMany(Comment::class, 'commentable');
    }

    /**
     * @param Model $commentable
     * @param string $content
     * @return Model|Comment
     */
    public function commentTo(Model $commentable, string $content)
    {
        return $this->comments()->create([
            'commentable_id' => $commentable->getKey(),
            'commentable_type' => $commentable->getMorphClass(),
            'content' => $content,
        ]);
    }
}
