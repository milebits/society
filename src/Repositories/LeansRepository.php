<?php


namespace Milebits\Society\Repositories;


use App\Models\Lean;
use Exception;
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

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function dislike(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::DISLIKE);
    }

    /**
     * @param Model $model
     * @return bool
     * @throws Exception
     */
    public function delete(Model $model): bool
    {
        return Lean::whereBetweenModels($this->model(), $model)->first()->delete();
    }

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function heart(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::HEART);
    }

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function laugh(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::LAUGH);
    }

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function like(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::LIKE);
    }

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function sad(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::SAD);
    }

    /**
     * @param Model $model
     * @return Lean|null
     * @throws Exception
     */
    public function cry(Model $model): ?Lean
    {
        return $this->newLean($model, Lean::CRY);
    }

    /**
     * @param Model $model
     * @param string $status
     * @return Model|Lean
     * @throws Exception
     */
    public function newLean(Model $model, string $status): Model
    {
        $this->delete($model);
        return $this->all()->create([
            'leanable_id' => $model->getKey(),
            'leanable_type' => $model->getMorphClass(),
            'status' => $status,
        ]);
    }
}
