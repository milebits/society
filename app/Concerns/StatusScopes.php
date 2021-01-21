<?php


namespace Milebits\Society\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use function Milebits\Society\Helpers\constVal;

/**
 * Trait StatusScopes
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait StatusScopes
{
    public function initializeStatusScopes()
    {
        $this->mergeFillable([$this->getStatusColumn()]);
    }

    /**
     * @return string
     */
    public function getStatusColumn(): string
    {
        return constVal($this, 'STATUS_COLUMN', 'status');
    }

    /**
     * @return string
     */
    public function getQualifiedStatusColumn(): string
    {
        return $this->qualifyColumn($this->getStatusColumn());
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideStatusColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedStatusColumn()
            : $this->getStatusColumn();
    }

    /**
     * @param Builder $builder
     * @param string $status
     * @return Builder
     */
    public function scopeWhereStatus(Builder $builder, string $status): Builder
    {
        return $builder->where(function (Builder $builder) use ($status): Builder {
            return $builder->where($this->decideStatusColumn($builder), $status);
        });
    }

    /**
     * @param Builder $builder
     * @param string $status
     * @return Builder
     */
    public function scopeOrWhereStatus(Builder $builder, string $status): Builder
    {
        return $builder->orWhere(function (Builder $builder) use ($status): Builder {
            return $builder->where($this->decideStatusColumn($builder), $status);
        });
    }

    /**
     * @param Builder $builder
     * @param string $status
     * @return Builder
     */
    public function scopeWhereNotStatus(Builder $builder, string $status): Builder
    {
        return $builder->where(function (Builder $builder) use ($status): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', $status);
        });
    }

    /**
     * @param Builder $builder
     * @param string $status
     * @return Builder
     */
    public function scopeOrWhereNotStatus(Builder $builder, string $status): Builder
    {
        return $builder->orWhere(function (Builder $builder) use ($status): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', $status);
        });
    }
}
