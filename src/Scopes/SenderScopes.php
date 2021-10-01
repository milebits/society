<?php


namespace Milebits\Society\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use function constVal;

/**
 * Trait SenderScopes
 * @package Milebits\Society\Scopes
 * @mixin Model
 */
trait SenderScopes
{
    public function initializeSenderScopes()
    {
        $this->mergeFillable([
            $this->getSenderIdColumn(), $this->getSenderTypeColumn(),
        ]);
    }

    /**
     * @return string
     */
    public function getSenderMorph(): string
    {
        return constVal($this, "SENDER_MORPH", 'sender');
    }

    /**
     * @return MorphTo
     */
    public function sender(): MorphTo
    {
        return $this->morphTo($this->getSenderMorph());
    }

    /**
     * @return string
     */
    public function getSenderIdColumn(): string
    {
        return sprintf("%s_id", $this->getSenderMorph());
    }

    /**
     * @return string
     */
    public function getSenderTypeColumn(): string
    {
        return sprintf("%s_type", $this->getSenderMorph());
    }

    /**
     * @return string
     */
    public function getQualifiedSenderIdColumn(): string
    {
        return $this->qualifyColumn($this->getSenderIdColumn());
    }

    /**
     * @return string
     */
    public function getQualifiedSenderTypeColumn(): string
    {
        return $this->qualifyColumn($this->getSenderTypeColumn());
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideSenderIdColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedSenderIdColumn()
            : $this->getSenderIdColumn();
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideSenderTypeColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedSenderTypeColumn()
            : $this->getSenderTypeColumn();
    }

    /**
     * @param Builder $builder
     * @param Model $sender
     * @return Builder
     */
    public function scopeWhereSenderIs(Builder $builder, Model $sender): Builder
    {
        return $builder->where(function (Builder $builder) use ($sender) {
            return $builder->where($this->decideSenderIdColumn($builder), '=', $sender->{$sender->getKeyName()})
                ->where($this->decideSenderTypeColumn($builder), '=', $sender->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $sender
     * @return Builder
     */
    public function scopeWhereSenderIsNot(Builder $builder, Model $sender): Builder
    {
        return $builder->where(function (Builder $builder) use ($sender): Builder {
            return $builder->where($this->decideSenderIdColumn($builder), '!=', $sender->{$sender->getKeyName()})
                ->where($this->decideSenderTypeColumn($builder), '!=', $sender->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $senders
     * @return Builder
     */
    public function scopeWhereSenderIsIn(Builder $builder, $senders): Builder
    {
        $senders = ($senders instanceof Collection) ? $senders : collect(is_array($senders) ? $senders : [$senders]);
        return $builder->where(function ($builder) use ($senders) {
            $senders = $senders->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($senders as $sender) {
                $builder->orWhereSenderIs($sender);
            }
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $senders
     * @return Builder
     */
    public function scopeWhereSenderIsNotIn(Builder $builder, $senders): Builder
    {
        $senders = ($senders instanceof Collection) ? $senders : collect(is_array($senders) ? $senders : [$senders]);
        return $builder->where(function ($builder) use ($senders) {
            $senders = $senders->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($senders as $sender)
                $builder->whereSenderIsNot($sender);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param Model $sender
     * @return Builder
     */
    public function scopeOrWhereSenderIs(Builder $builder, Model $sender): Builder
    {
        return $builder->orWhere(function ($builder) use ($sender) {
            return $builder->whereSenderIs($sender);
        });
    }

    /**
     * @param Builder $builder
     * @param Model $sender
     * @return Builder
     */
    public function scopeOrWhereSenderIsNot(Builder $builder, Model $sender): Builder
    {
        return $builder->orWhere(function ($builder) use ($sender): Builder {
            return $builder->whereSenderIsNot($sender);
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $senders
     * @return Builder
     */
    public function scopeOrWhereSenderIsIn(Builder $builder, $senders): Builder
    {
        $senders = ($senders instanceof Collection) ? $senders : collect(is_array($senders) ? $senders : [$senders]);
        return $builder->orWhere(function ($builder) use ($senders) {
            $senders = $senders->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($senders as $sender)
                $builder->orWhereSenderIs($sender);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $senders
     * @return Builder
     */
    public function scopeOrWhereSenderIsNotIn(Builder $builder, $senders): Builder
    {
        $senders = ($senders instanceof Collection) ? $senders : collect(is_array($senders) ? $senders : [$senders]);
        return $builder->orWhere(function ($builder) use ($senders) {
            $senders = $senders->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($senders as $sender)
                $builder->whereSenderIsNot($sender);
            return $builder;
        });
    }
}