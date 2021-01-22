<?php


namespace Milebits\Society\Repositories;


use App\Models\Lean;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LeansRepository extends ChildRepository
{
    /**
     * @return MorphMany
     */
    public function all()
    {
        return $this->model()->morphMany(Lean::class, "owner");
    }

    public function dislike(Model $model): ?Lean
    {

    }

    public function delete(Model $model): bool
    {

    }

    public function heart(Model $model): ?Lean
    {

    }

    public function laugh(Model $model): ?Lean
    {

    }

    public function like(Model $model): ?Lean
    {

    }

    public function sad(Model $model): ?Lean
    {

    }

    public function cry(Model $model): ?Lean
    {

    }

    public function findExistingLean(Model $model): ?Lean
    {

    }

    public function newLean(Model $model, string $status): Lean
    {

    }
}
