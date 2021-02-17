<?php


namespace Milebits\Society\Repositories;


use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Milebits\Society\Models\Message;

class MessagesRepository extends ChildRepository
{
    /**
     * Create a new Message instance
     *
     * @param Model $recipient
     * @param array $data
     * @return Model|Message|null
     */
    protected function newMessage(Model $recipient, array $data): ?Model
    {
        return $this->sent()->create(array_merge([
            'recipient_id' => $recipient->getKey(),
            'recipient_type' => $recipient->getMorphClass(),
        ], $data));
    }

    /**
     * Send a message
     *
     * @param Model $friend
     * @param string|array|Collection|Arrayable|null $attachment
     * @param string $content
     * @return Model|Message
     */
    public function send(Model $friend, string $content = '', $attachment = null): ?Message
    {
        if (!$this->canSendMessageTo($friend)) return null;
        $message = $this->newMessage($friend, ['content' => $content]);
        if (!is_null($attachment)) {
            if (is_string($attachment)) $attachment = ['attachment' => $attachment];
            if (is_array($attachment)) $attachment = collect($attachment);
            if ($attachment instanceof Arrayable)
                $message->attachments()->createMany($attachment->toArray());
        }
        return $message;
    }

    /**
     * Mark a message as delivered
     *
     * @param Message $message
     * @return bool
     */
    public function markAsDelivered(Message $message)
    {
        return $message->update(['delivered_at' => now()]);
    }

    /**
     * Mark a message as seen
     *
     * @param Message $message
     * @return bool
     */
    public function markAsSeen(Message $message)
    {
        return $message->update(['seen_at' => now()]);
    }

    /**
     * Delete a certain message
     *
     * @param Message $message
     * @return bool|null
     * @throws Exception
     */
    public function delete(Message $message)
    {
        if (!$this->canOperateMessage($message)) return false;
        return $message->delete();
    }

    /**
     * Respond to a message
     *
     * @param Message $message
     * @param string $content
     * @param string|null $attachment
     * @return Model|Message
     */
    public function respond(Message $message, string $content, string $attachment = null)
    {
        $message = $this->newMessage($message->sender()->first(), ['content' => $content]);
        $message->parentMessage()->associate($message);
        if (!is_null($attachment))
            $message->attachments()->create(['path' => $attachment]);
        return $message;
    }

    /**
     * All messages sent or received by this user
     *
     * @return Builder
     */
    public function all()
    {
        return Message::whereSenderIs($this->model())->orWhereRecipientIs($this->model());
    }

    /**
     * Sent messages
     *
     * @return MorphMany
     */
    public function sent()
    {
        return $this->model()->morphMany(Message::class, "sender");
    }

    /**
     * Received messages
     *
     * @return MorphMany
     */
    public function received()
    {
        return $this->model()->morphMany(Message::class, 'recipient');
    }

    /**
     * Check is whether the user can interact with this message and modify it's content
     *
     * @param Message $message
     * @return bool
     */
    public function canOperateMessage(Message $message): bool
    {
        $sender = $message->sender()->first();
        if (is_null($sender)) return false;
        return $sender->is($this->model());
    }

    /**
     * Check if whether this user can send a message to a user
     *
     * @param Model $person
     * @return bool
     */
    public function canSendMessageTo(Model $person): bool
    {
        return !$this->society->friends->isBlockedBy($person);
    }
}