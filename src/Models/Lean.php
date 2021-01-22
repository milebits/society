<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Milebits\Society\Concerns\StatusScopes;
use function Milebits\Society\Helpers\constVal;

/**
 * Class Lean
 * @package App\Models
 * @property string $status
 */
class Lean extends Model
{
    use HasFactory, StatusScopes;

    protected $fillable = [
        'owner_id', 'leanable_id', 'status',
        'owner_type', 'leanable_type'
    ];

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
    public function owner()
    {
        return $this->morphTo();
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

    /**
     * @return string
     */
    public function getOwnerIdColumn(): string
    {
        return constVal($this, sprintf("%s_id", self::OWNER_MORPH), 'owner_id');
    }

    /**
     * @return string
     */
    public function getOwnerTypeColumn(): string
    {
        return constVal($this, sprintf("%s_type", self::OWNER_MORPH), 'owner_type');
    }

    /**
     * @return string
     */
    public function getQualifiedOwnerIdColumn(): string
    {
        return $this->qualifyColumn($this->getOwnerIdColumn());
    }

    /**
     * @return string
     */
    public function getQualifiedOwnerTypeColumn(): string
    {
        return $this->qualifyColumn($this->getOwnerTypeColumn());
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideOwnerIdColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedOwnerIdColumn()
            : $this->getOwnerIdColumn();
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideOwnerTypeColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedOwnerTypeColumn()
            : $this->getOwnerTypeColumn();
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeWhereOwnerIs(Builder $builder, Model $owner): Builder
    {
        return $builder->where(function (Builder $builder) use ($owner) {
            return $builder->where($this->decideOwnerIdColumn($builder), '=', $owner->{$owner->getKeyName()})
                ->where($this->decideOwnerTypeColumn($builder), '=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeWhereOwnerIsNot(Builder $builder, Model $owner): Builder
    {
        return $builder->where(function (Builder $builder) use ($owner): Builder {
            return $builder->where($this->decideOwnerIdColumn($builder), '!=', $owner->{$owner->getKeyName()})
                ->where($this->decideOwnerTypeColumn($builder), '!=', $owner->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $owners
     * @return Builder
     */
    public function scopeWhereOwnerIsIn(Builder $builder, $owners): Builder
    {
        $owners = ($owners instanceof Collection) ? $owners : collect(is_array($owners) ? $owners : [$owners]);
        return $builder->where(function ($builder) use ($owners) {
            $owners = $owners->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($owners as $owner) {
                $builder->orWhereOwnerIs($owner);
            }
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $owners
     * @return Builder
     */
    public function scopeWhereOwnerIsNotIn(Builder $builder, $owners): Builder
    {
        $owners = ($owners instanceof Collection) ? $owners : collect(is_array($owners) ? $owners : [$owners]);
        return $builder->where(function ($builder) use ($owners) {
            $owners = $owners->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($owners as $owner)
                $builder->whereOwnerIsNot($owner);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeOrWhereOwnerIs(Builder $builder, Model $owner): Builder
    {
        return $builder->orWhere(function ($builder) use ($owner) {
            return $builder->whereOwnerIs($owner);
        });
    }

    /**
     * @param Builder $builder
     * @param Model $owner
     * @return Builder
     */
    public function scopeOrWhereOwnerIsNot(Builder $builder, Model $owner): Builder
    {
        return $builder->orWhere(function ($builder) use ($owner): Builder {
            return $builder->whereOwnerIsNot($owner);
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $owners
     * @return Builder
     */
    public function scopeOrWhereOwnerIsIn(Builder $builder, $owners): Builder
    {
        $owners = ($owners instanceof Collection) ? $owners : collect(is_array($owners) ? $owners : [$owners]);
        return $builder->orWhere(function ($builder) use ($owners) {
            $owners = $owners->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($owners as $owner)
                $builder->orWhereOwnerIs($owner);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $owners
     * @return Builder
     */
    public function scopeOrWhereOwnerIsNotIn(Builder $builder, $owners): Builder
    {
        $owners = ($owners instanceof Collection) ? $owners : collect(is_array($owners) ? $owners : [$owners]);
        return $builder->orWhere(function ($builder) use ($owners) {
            $owners = $owners->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($owners as $owner)
                $builder->whereOwnerIsNot($owner);
            return $builder;
        });
    }
}
