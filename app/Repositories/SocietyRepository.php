<?php

namespace Milebits\Society\Repositories;

use Illuminate\Database\Eloquent\Model;

class SocietyRepository
{
    /**
     * Parent model pointer in order to follow the advancements
     *
     * @var Model|null
     */
    protected ?Model $parent = null;

    public function __construct(Model &$parent)
    {
        $this->parent = &$parent;
    }

    /**
     * @return Model|null
     */
    public function parent(): ?Model
    {
        return $this->parent;
    }
}