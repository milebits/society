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

    protected ?FriendsRepository $friends = null;

    /**
     * SocietyRepository constructor.
     * @param Sociable|Model $parent
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->friends = new FriendsRepository($this);
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
}
