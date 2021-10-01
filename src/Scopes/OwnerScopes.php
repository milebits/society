<?php

namespace Milebits\Society\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use function constVal;

/**
 * Trait OwnerScopes
 * @package Milebits\Society\Scopes
 * @mixin Model
 */
trait OwnerScopes
{
    public function initializeOwnerScopes()
    {
        $this->mergeFillable([$this->getOwnerIdColumn(), $this->getOwnerTypeColumn()]);
    }

    /**
     * @return MorphTo
     */
    public function owner()
    {
        return $this->morphTo();
    }

    /**
     * @return string
     */
    public function getOwnerIdColumn(): string
    {
        return sprintf("%s_id", constVal($this, "OWNER_MORPH", 'owner'));
    }

    /**
     * @return string
     */
    public function getOwnerTypeColumn(): string
    {
        return sprintf("%s_type", constVal($this, "OWNER_MORPH", 'owner'));
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