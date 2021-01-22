<?php


namespace Milebits\Society\Repositories;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Milebits\Society\Concerns\Leanable;
use Milebits\Society\Models\Lean;

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
        $lean = Lean::whereBetweenModels($this->model(), $model)->first();
        if (is_null($lean)) return true;
        return $lean->delete();
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
     * @return Model|Lean|null
     * @throws Exception
     */
    public function newLean(Model $model, string $status): ?Model
    {
        if (!$this->delete($model)) return null;
        if (!($model instanceof Leanable)) return null;
        return $this->all()->create([
            'leanable_id' => $model->getKey(),
            'leanable_type' => $model->getMorphClass(),
            'status' => $status,
        ]);
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function liked(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::LIKE)->exists();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function disliked(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::DISLIKE)->exists();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function hearted(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::HEART)->exists();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function laughedAt(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::LAUGH)->exists();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function criedAt(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::CRY)->exists();
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function sadOver(Model $model)
    {
        return Lean::whereOwnerIs($this->model())->whereLeanableIs($model)->whereStatus(Lean::SAD)->exists();
    }
}
