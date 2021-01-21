<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Milebits\Society\Concerns\StatusScopes;
use function Milebits\Society\Helpers\constVal;

/**
 * Class FriendRequest
 * @package Milebits\Society\Models
 *
 * @property string $status
 */
class FriendRequest extends Model
{
    use HasFactory, SoftDeletes, StatusScopes;

    const PENDING = "pending";
    const ACCEPTED = "accepted";
    const DENIED = "denied";
    const BLOCKED = "blocked";
    const RECIPIENT_MORPH = "recipient";
    const SENDER_MORPH = "sender";

    /**
     * @return MorphTo
     */
    public function sender(): MorphTo
    {
        return $this->morphTo(self::SENDER_MORPH);
    }

    /**
     * @return MorphTo
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo(self::RECIPIENT_MORPH);
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
        return $this->status === $status;
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

    /**
     * @return string
     */
    public function getRecipientIdColumn(): string
    {
        return constVal($this, sprintf("%s_id", self::RECIPIENT_MORPH), 'recipient_id');
    }

    /**
     * @return string
     */
    public function getRecipientTypeColumn(): string
    {
        return constVal($this, sprintf("%s_type", self::RECIPIENT_MORPH), 'recipient_type');
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

    /**
     * @return string
     */
    public function getSenderIdColumn(): string
    {
        return constVal($this, sprintf("%s_id", self::SENDER_MORPH), 'sender_id');
    }

    /**
     * @return string
     */
    public function getSenderTypeColumn(): string
    {
        return constVal($this, sprintf("%s_type", self::SENDER_MORPH), 'sender_type');
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
