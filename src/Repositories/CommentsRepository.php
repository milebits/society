<?php


namespace Milebits\Society\Repositories;


use Milebits\Society\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CommentsRepository extends ChildRepository
{
    /**
     * @param Model $model
     * @param array $data
     * @param array|null $attachments
     * @return Model|Comment
     */
    public function add(Model $model, array $data, array $attachments = null): Model
    {
        $comment = $this->newComment(array_merge($data, [
            'commentable_id' => $model->getKey(),
            'commentable_type' => $model->getMorphClass(),
        ]));
        if (!is_null($attachments))
            $comment->attachments()->createMany($attachments);
        return $comment;
    }

    /**
     * @return MorphMany
     */
    public function all(): MorphMany
    {
        return $this->model()->morphMany(Comment::class, "owner");
    }

    /**
     * @param array $data
     * @return Model|Comment
     */
    public function newComment(array $data)
    {
        return $this->all()->create($data);
    }
}
