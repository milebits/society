<?php


namespace Milebits\Society\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use function constVal;

/**
 * Trait RecipientScopes
 * @package Milebits\Society\Scopes
 * @mixin Model
 */
trait RecipientScopes
{
    public function initializeRecipientScopes()
    {
        $this->mergeFillable([
            $this->getRecipientIdColumn(), $this->getRecipientTypeColumn(),
        ]);
    }

    /**
     * @return MorphTo
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo($this->getRecipientMorph());
    }

    /**
     * @return string
     */
    public function getRecipientMorph(): string
    {
        return constVal($this, 'RECIPIENT_MORPH', 'recipient');
    }

    /**
     * @return string
     */
    public function getRecipientIdColumn(): string
    {
        return sprintf("%s_id", $this->getRecipientMorph());
    }

    /**
     * @return string
     */
    public function getRecipientTypeColumn(): string
    {
        return sprintf("%s_type", $this->getRecipientMorph());
    }

    /**
     * @return string
     */
    public function getQualifiedRecipientIdColumn(): string
    {
        return $this->qualifyColumn($this->getRecipientIdColumn());
    }

    /**
     * @return string
     */
    public function getQualifiedRecipientTypeColumn(): string
    {
        return $this->qualifyColumn($this->getRecipientTypeColumn());
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideRecipientIdColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedRecipientIdColumn()
            : $this->getRecipientIdColumn();
    }

    /**
     * @param Builder $builder
     * @return string
     */
    public function decideRecipientTypeColumn(Builder $builder): string
    {
        return count((array)(property_exists($builder, 'joins') ? $builder->joins : [])) > 0
            ? $this->getQualifiedRecipientTypeColumn()
            : $this->getRecipientTypeColumn();
    }

    /**
     * @param Builder $builder
     * @param Model $recipient
     * @return Builder
     */
    public function scopeWhereRecipientIs(Builder $builder, Model $recipient): Builder
    {
        return $builder->where(function (Builder $builder) use ($recipient) {
            return $builder->where($this->decideRecipientIdColumn($builder), '=', $recipient->{$recipient->getKeyName()})
                ->where($this->decideRecipientTypeColumn($builder), '=', $recipient->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Model $recipient
     * @return Builder
     */
    public function scopeWhereRecipientIsNot(Builder $builder, Model $recipient): Builder
    {
        return $builder->where(function (Builder $builder) use ($recipient): Builder {
            return $builder->where($this->decideRecipientIdColumn($builder), '!=', $recipient->{$recipient->getKeyName()})
                ->where($this->decideRecipientTypeColumn($builder), '!=', $recipient->getMorphClass());
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $recipients
     * @return Builder
     */
    public function scopeWhereRecipientIsIn(Builder $builder, $recipients): Builder
    {
        $recipients = ($recipients instanceof Collection) ? $recipients : collect(is_array($recipients) ? $recipients : [$recipients]);
        return $builder->where(function ($builder) use ($recipients) {
            $recipients = $recipients->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($recipients as $recipient) {
                $builder->orWhereRecipientIs($recipient);
            }
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $recipients
     * @return Builder
     */
    public function scopeWhereRecipientIsNotIn(Builder $builder, $recipients): Builder
    {
        $recipients = ($recipients instanceof Collection) ? $recipients : collect(is_array($recipients) ? $recipients : [$recipients]);
        return $builder->where(function ($builder) use ($recipients) {
            $recipients = $recipients->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($recipients as $recipient)
                $builder->whereRecipientIsNot($recipient);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param Model $recipient
     * @return Builder
     */
    public function scopeOrWhereRecipientIs(Builder $builder, Model $recipient): Builder
    {
        return $builder->orWhere(function ($builder) use ($recipient) {
            return $builder->whereRecipientIs($recipient);
        });
    }

    /**
     * @param Builder $builder
     * @param Model $recipient
     * @return Builder
     */
    public function scopeOrWhereRecipientIsNot(Builder $builder, Model $recipient): Builder
    {
        return $builder->orWhere(function ($builder) use ($recipient): Builder {
            return $builder->whereRecipientIsNot($recipient);
        });
    }

    /**
     * @param Builder $builder
     * @param Collection|Model[]|Model $recipients
     * @return Builder
     */
    public function scopeOrWhereRecipientIsIn(Builder $builder, $recipients): Builder
    {
        $recipients = ($recipients instanceof Collection) ? $recipients : collect(is_array($recipients) ? $recipients : [$recipients]);
        return $builder->orWhere(function ($builder) use ($recipients) {
            $recipients = $recipients->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($recipients as $recipient)
                $builder->orWhereRecipientIs($recipient);
            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @param $recipients
     * @return Builder
     */
    public function scopeOrWhereRecipientIsNotIn(Builder $builder, $recipients): Builder
    {
        $recipients = ($recipients instanceof Collection) ? $recipients : collect(is_array($recipients) ? $recipients : [$recipients]);
        return $builder->orWhere(function ($builder) use ($recipients) {
            $recipients = $recipients->transform(function (Model $item) {
                return (object)['key' => $item->{$item->getKeyName()}, 'type' => $item->getMorphClass()];
            });
            foreach ($recipients as $recipient)
                $builder->whereRecipientIsNot($recipient);
            return $builder;
        });
    }
}