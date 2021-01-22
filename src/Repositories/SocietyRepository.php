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
     * SocietyRepository constructor.
     * @param Sociable|Model $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->friends = new FriendsRepository($this);
        $this->comments = new CommentsRepository($this);
        $this->leans = new LeansRepository($this);
        $this->stories = new StoriesRepository($this);
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
}
