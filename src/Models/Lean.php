<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Milebits\Society\Scopes\OwnerScopes;
use Milebits\Society\Scopes\StatusScopes;
use function Milebits\Society\Helpers\constVal;

/**
 * Class Lean
 * @package App\Models
 * @property string $status
 */
class Lean extends Model
{
    use HasFactory, StatusScopes, OwnerScopes;

    protected $fillable = ['leanable_id', 'leanable_type'];

    const OWNER_MORPH = "owner";
    const LEANABLE_MORPH = "leanable";

    const DISLIKE = "dislike";
    const HEART = "heart";
    const LAUGH = "laugh";
    const LIKE = "like";
    const SAD = "sad";
    const CRY = "cry";

    /**
     * @param string $status
     * @return $this
     */
    public function markAs(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return $this
     */
    public function dislike()
    {
        return $this->markAs(self::DISLIKE);
    }

    /**
     * @return $this
     */
    public function heart()
    {
        return $this->markAs(self::HEART);
    }

    /**
     * @return $this
     */
    public function laugh()
    {
        return $this->markAs(self::LAUGH);
    }

    /**
     * @return $this
     */
    public function like()
    {
        return $this->markAs(self::LIKE);
    }

    /**
     * @return $this
     */
    public function sad()
    {
        return $this->markAs(self::SAD);
    }

    /**
     * @return $this
     */
    public function cry()
    {
        return $this->markAs(self::CRY);
    }

    /**
     * @return MorphTo
     */
    public function leanable()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $owner
     * @param ?Model $leanable
     * @return Builder|null
     */
    public function scopeWhereBetweenModels(Builder $builder, $owner, ?Model $leanable = null): ?Builder
    {
        if ((is_null($owner) || !is_array($owner)) && is_null($leanable))
            return $builder;
        if (is_array($owner) && is_null($leanable))
            [$owner, $leanable] = $owner;
        return $builder->where(function (Builder $builder) use ($owner, $leanable): Builder {
            return $builder->where(function ($builder) use ($owner, $leanable): Builder {
                return $builder->whereOwnerIs($owner)->whereLeanableIs($leanable);
            })->orWhere(function ($builder) use ($owner, $leanable): Builder {
                return $builder->whereOwnerIs($leanable)->whereLeanableIs($owner);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $owner
     * @param ?Model $leanable
     * @return Builder|null
     */
    public function scopeOrWhereBetweenModels(Builder $builder, $owner, ?Model $leanable = null): ?Builder
    {
        if (is_null($owner) && is_null($leanable)) return $builder;
        if (is_null($leanable) && is_array($owner)) [$owner, $leanable] = $owner;
        return $builder->orWhere(function (Builder $builder) use ($owner, $leanable): Builder {
            return $builder->where(function ($builder) use ($owner, $leanable): Builder {
                return $builder->whereOwnerIs($owner)->whereLeanableIs($leanable);
            })->orWhere(function ($builder) use ($owner, $leanable): Builder {
                return $builder->whereOwnerIs($leanable)->whereLeanableIs($owner);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $owner
     * @param ?Model $leanable
     * @return Builder|null
     */
    public function scopeWhereNotBetweenModels(Builder $builder, $owner, ?Model $leanable = null): ?Builder
    {
        if (is_null($owner) && is_null($leanable)) return $builder;
        if (is_null($leanable) && is_array($owner)) [$owner, $leanable] = $owner;
        return $builder->where(function (Builder $builder) use ($owner, $leanable): Builder {
            return $builder->where(function ($builder) use ($owner, $leanable) {
                return $builder->whereOwnerIsNot($owner)->whereLeanableIsNot($leanable);
            })->orWhere(function ($builder) use ($owner, $leanable) {
                return $builder->whereOwnerIsNot($leanable)->whereLeanableIsNot($owner);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $owner
     * @param ?Model $leanable
     * @return Builder|null
     */
    public function scopeOrWhereNotBetweenModels(Builder $builder, $owner, ?Model $leanable = null): ?Builder
    {
        if (is_null($owner) && is_null($leanable)) return $builder;
        if (is_null($leanable) && is_array($owner)) [$owner, $leanable] = $owner;
        return $builder->orWhere(function (Builder $builder) use ($owner, $leanable): Builder {
            return $builder->where(function ($builder) use ($owner, $leanable) {
                return $builder->whereOwnerIsNot($owner)->whereLeanableIsNot($leanable);
            })->orWhere(function ($builder) use ($owner, $leanable) {
                return $builder->whereOwnerIsNot($leanable)->whereLeanableIsNot($owner);
            });
        });
    }

    /**
     * @return string
     */
    public function getLeanableIdColumn(): string
    {
        return constVal($this, sprintf("%s_id", self::LEANABLE_MORPH), 'leanable_id');
    }

    /**
     * @return string
     */
    public function getLeanableTypeColumn(): string
    {
        return constVal($this, sprintf("%s_type", self::LEANABLE_MORPH), 'leanable_type');
    }

    /**
     * @return string
     */
    public function getQualifiedLeanableIdColumn(): string
    {
        return $this->qualifyColumn($this->getLeanableIdColumn());
    }

    /**
     * @return string
     */
    public function getQualifiedLeanableTypeColumn(): string
    {
        return $this->qualifyColumn($this->getLeanableTypeColumn());
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideLeanableIdColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedLeanableIdColumn()
            : $this->getLeanableIdColumn();
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideLeanableTypeColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedLeanableTypeColumn()
            : $this->getLeanableTypeColumn();
    }

    /**
     * @param Builder $builder
     * @param Model $leanable
     * @return Builder
     */
    public function scopeWhereLeanableIs(Builder $builder, Model $leanable): Builder
    {
        return $builder->where(function (Builder $builder) use ($leanable) {
            return $builder->where($this->decideLeanableIdColumn($builder), '=', $leanable->{$leanable->getKeyName()})
                ->where($this->decideLeanableTypeColumn($builder), '=', $leanable->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $leanable
     * @return Builder
     */
    public function scopeWhereLeanableIsNot(Builder $builder, Model $leanable): Builder
    {
        return $builder->where(function (Builder $builder) use ($leanable): Builder {
            return $builder->where($this->decideLeanableIdColumn($builder), '!=', $leanable->{$leanable->getKeyName()})
                ->where($this->decideLeanableTypeColumn($builder), '!=', $leanable->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $leanable
     * @return Builder
     */
    public function scopeWhereLeanableIsIn(Builder $builder, $leanable): Builder
    {
        $leanable = ($leanable instanceof Collection) ? $leanable : collect(is_array($leanable) ? $leanable : [$leanable]);
        return $builder->where(function ($builder) use ($leanable) {
            $leanable = $leanable->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($leanable as $model) {
                $builder->orWhereLeanableIs($model);
            }
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $leanable
     * @return Builder
     */
    public function scopeWhereLeanableIsNotIn(Builder $builder, $leanable): Builder
    {
        $leanable = ($leanable instanceof Collection) ? $leanable : collect(is_array($leanable) ? $leanable : [$leanable]);
        return $builder->where(function ($builder) use ($leanable) {
            $leanable = $leanable->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($leanable as $model)
                $builder->whereLeanableIsNot($model);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param Model $leanable
     * @return Builder
     */
    public function scopeOrWhereLeanableIs(Builder $builder, Model $leanable): Builder
    {
        return $builder->orWhere(function ($builder) use ($leanable) {
            return $builder->whereLeanableIs($leanable);
        });
    }

    /**
     * @param Builder $builder
     * @param Model $leanable
     * @return Builder
     */
    public function scopeOrWhereLeanableIsNot(Builder $builder, Model $leanable): Builder
    {
        return $builder->orWhere(function ($builder) use ($leanable): Builder {
            return $builder->whereLeanableIsNot($leanable);
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $leanable
     * @return Builder
     */
    public function scopeOrWhereLeanableIsIn(Builder $builder, $leanable): Builder
    {
        $leanable = ($leanable instanceof Collection) ? $leanable : collect(is_array($leanable) ? $leanable : [$leanable]);
        return $builder->orWhere(function ($builder) use ($leanable) {
            $leanable = $leanable->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($leanable as $model)
                $builder->orWhereLeanableIs($model);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $leanable
     * @return Builder
     */
    public function scopeOrWhereLeanableIsNotIn(Builder $builder, $leanable): Builder
    {
        $leanable = ($leanable instanceof Collection) ? $leanable : collect(is_array($leanable) ? $leanable : [$leanable]);
        return $builder->orWhere(function ($builder) use ($leanable) {
            $leanable = $leanable->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($leanable as $model)
                $builder->whereLeanableIsNot($model);
            return $builder;
        });
    }
}
