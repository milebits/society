<?php


namespace Milebits\Society\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait BetweenModelsScopes
 * @package Milebits\Society\Scopes
 * @mixin Model
 */
trait BetweenModelsScopes
{
    /**
     * @param Builder $builder
     * @param Model|Model[]|array $sender
     * @param ?Model $recipient
     * @return Builder|null
     */
    public function scopeWhereBetweenModels(Builder $builder, $sender, ?Model $recipient = null): ?Builder
    {
        if ((is_null($sender) || !is_array($sender)) && is_null($recipient))
            return $builder;
        if (is_array($sender) && is_null($recipient))
            [$sender, $recipient] = $sender;
        return $builder->where(function (Builder $builder) use ($sender, $recipient): Builder {
            return $builder->where(function ($builder) use ($sender, $recipient): Builder {
                return $builder->whereSenderIs($sender)->whereRecipientIs($recipient);
            })->orWhere(function ($builder) use ($sender, $recipient): Builder {
                return $builder->whereSenderIs($recipient)->whereRecipientIs($sender);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $sender
     * @param ?Model $recipient
     * @return Builder|null
     */
    public function scopeOrWhereBetweenModels(Builder $builder, $sender, ?Model $recipient = null): ?Builder
    {
        if (is_null($sender) && is_null($recipient)) return $builder;
        if (is_null($recipient) && is_array($sender)) [$sender, $recipient] = $sender;
        return $builder->orWhere(function (Builder $builder) use ($sender, $recipient): Builder {
            return $builder->where(function ($builder) use ($sender, $recipient): Builder {
                return $builder->whereSenderIs($sender)->whereRecipientIs($recipient);
            })->orWhere(function ($builder) use ($sender, $recipient): Builder {
                return $builder->whereSenderIs($recipient)->whereRecipientIs($sender);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $sender
     * @param ?Model $recipient
     * @return Builder|null
     */
    public function scopeWhereNotBetweenModels(Builder $builder, $sender, ?Model $recipient = null): ?Builder
    {
        if (is_null($sender) && is_null($recipient)) return $builder;
        if (is_null($recipient) && is_array($sender)) [$sender, $recipient] = $sender;
        return $builder->where(function (Builder $builder) use ($sender, $recipient): Builder {
            return $builder->where(function ($builder) use ($sender, $recipient) {
                return $builder->whereSenderIsNot($sender)->whereRecipientIsNot($recipient);
            })->orWhere(function ($builder) use ($sender, $recipient) {
                return $builder->whereSenderIsNot($recipient)->whereRecipientIsNot($sender);
            });
        });
    }

    /**
     * @param Builder $builder
     * @param Model|Model[]|array $sender
     * @param ?Model $recipient
     * @return Builder|null
     */
    public function scopeOrWhereNotBetweenModels(Builder $builder, $sender, ?Model $recipient = null): ?Builder
    {
        if (is_null($sender) && is_null($recipient)) return $builder;
        if (is_null($recipient) && is_array($sender)) [$sender, $recipient] = $sender;
        return $builder->orWhere(function (Builder $builder) use ($sender, $recipient): Builder {
            return $builder->where(function ($builder) use ($sender, $recipient) {
                return $builder->whereSenderIsNot($sender)->whereRecipientIsNot($recipient);
            })->orWhere(function ($builder) use ($sender, $recipient) {
                return $builder->whereSenderIsNot($recipient)->whereRecipientIsNot($sender);
            });
        });
    }
}