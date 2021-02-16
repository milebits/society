<?php


namespace Milebits\Society\Repositories;


use Illuminate\Database\Eloquent\Model;
use Milebits\Society\Concerns\Sociable;

abstract class ChildRepository
{
    /**
     * @var Model|Sociable|null
     */
    protected ?Model $model = null;

    /**
     * @var SocietyRepository|null
     */
    protected ?SocietyRepository $society = null;

    public function __construct(SocietyRepository $society)
    {
        $this->society = $society;
        $this->model = $this->society->parent();
    }

    /**
     * @return Model|Sociable|null
     */
    public function model(): ?Model
    {
        return $this->model;
    }

    /**
     * @return SocietyRepository|null
     */
    public function society(): ?SocietyRepository
    {
        return $this->society;
    }
}
