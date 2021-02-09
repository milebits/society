<?php

namespace Milebits\Society\Repositories;

use Illuminate\Database\Eloquent\Model;
use Milebits\Society\Concerns\Sociable;

class SocietyRepository
{
    /**
     * Parent model pointer in order to follow the advancements
     *
     * @var Model|null
     */
    protected ?Model $parent = null;

    /**
     * @var FriendsRepository|null
     */
    public ?FriendsRepository $friends = null;

    /**
     * @var CommentsRepository|null
     */
    public ?CommentsRepository $comments = null;

    /**
     * @var LeansRepository|null
     */
    public ?LeansRepository $leans = null;

    /**
     * @var StoriesRepository|null
     */
    public ?StoriesRepository $stories = null;

    /**
     * @var MessagesRepository|null
     */
    public ?MessagesRepository $messages = null;

    /**
     * SocietyRepository constructor.
     * @param Sociable|Model $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->friends = $this->buildRepository('friends');
        $this->comments = $this->buildRepository('comments');
        $this->leans = $this->buildRepository('leans');
        $this->stories = $this->buildRepository('stories');
        $this->messages = $this->buildRepository('messages');
    }

    /**
     * @return Model|Sociable|null
     */
    public function parent(): ?Model
    {
        return $this->parent;
    }

    /**
     * @return FriendsRepository|null
     */
    public function friends()
    {
        return $this->friends;
    }

    /**
     * @return CommentsRepository|null
     */
    public function comments()
    {
        return $this->comments;
    }

    /**
     * @return LeansRepository|null
     */
    public function leans()
    {
        return $this->leans;
    }

    /**
     * @return StoriesRepository|null
     */
    public function stories()
    {
        return $this->stories;
    }

    /**
     * @return MessagesRepository|null
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * @param string $repository
     * @return ChildRepository|mixed
     */
    protected function buildRepository(string $repository)
    {
        return new (config(sprintf("society.repositories.%s", $repository)))($this);
    }
}
