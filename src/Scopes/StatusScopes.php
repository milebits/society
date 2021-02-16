<?php


namespace Milebits\Society\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use function Milebits\Helpers\Helpers\constVal;

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
    public function markAsPending(): self
    {
        return $this->markAs(self::PENDING);
    }

    /**
     * @return $this
     */
    public function markAsDenied(): self
    {
        return $this->markAs(self::DENIED);
    }

    /**
     * @return $this
     */
    public function markAsAccepted(): self
    {
        return $this->markAs(self::ACCEPTED);
    }

    /**
     * @return $this
     */
    public function markAsBlocked(): self
    {
        return $this->markAs(self::BLOCKED);
    }

    /**
     * @param string $status
     * @return bool
     */
    public function statusIs(string $status): bool
    {
        return $this->{$this->getStatusColumn()} === $status;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->statusIs(self::PENDING);
    }

    /**
     * @return bool
     */
    public function isDenied()
    {
        return $this->statusIs(self::DENIED);
    }

    /**
     * @return bool
     */
    public function isAccepted()
    {
        return $this->statusIs(self::ACCEPTED);
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->statusIs(self::BLOCKED);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopePending(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::PENDING);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeDenied(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::DENIED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeAccepted(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::ACCEPTED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeBlocked(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::BLOCKED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotPending(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::PENDING);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotDenied(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::DENIED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotAccepted(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::ACCEPTED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeNotBlocked(Builder $builder): Builder
    {
        return $builder->where(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::BLOCKED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrPending(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::PENDING);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrDenied(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::DENIED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrAccepted(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::ACCEPTED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrBlocked(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), self::BLOCKED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrNotPending(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::PENDING);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrNotDenied(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::DENIED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrNotAccepted(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::ACCEPTED);
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOrNotBlocked(Builder $builder): Builder
    {
        return $builder->orWhere(function (Builder $builder): Builder {
            return $builder->where($this->decideStatusColumn($builder), '!=', self::BLOCKED);
        });
    }
}
