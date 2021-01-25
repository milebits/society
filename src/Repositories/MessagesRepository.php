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
     * @param Model $recipient
     * @param array $data
     * @return Model|Message|null
     */
    protected function newMessage(Model $recipient, array $data): ?Model
    {
        return $this->all()->create(array_merge([
            'recipient_id' => $recipient->getKey(),
            'recipient_type' => $recipient->getMorphClass(),
        ], $data));
    }

    /**
     * @param Model $friend
     * @param string $content
     * @return Model|Message|null
     */
    public function sendTo(Model $friend, string $content)
    {
        return $this->newMessage($friend, compact($content));
    }

    /**
     * @param Model $friend
     * @param string|array|Collection|Arrayable $attachment
     * @param string $content
     * @return Collection
     */
    public function sendAttachmentTo(Model $friend, $attachment, string $content = '')
    {
        if (is_string($attachment)) $attachment = [compact($attachment)];
        if (is_array($attachment)) $attachment = collect($attachment);
        return $this->sendTo($friend, $content)->attachments()->createMany($attachment->toArray());
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function sendDeliveryNotificationFor(Message $message)
    {
        return $message->update(['delivered_at' => now()]);
    }

    /**
     * @param Message $message
     * @return bool
     */
    public function sendSeenNotificationFor(Message $message)
    {
        return $message->update(['seen_at' => now()]);
    }

    /**
     * @param Message $message
     * @return bool|null
     * @throws Exception
     */
    public function delete(Message $message)
    {
        return $message->delete();
    }

    /**
     * @param Message $message
     * @param string $content
     * @param string|null $attachment
     * @return Model|Message
     */
    public function respondToMessage(Message $message, string $content, string $attachment = null)
    {
        $message = $this->newMessage($message->sender()->first(), compact($content));
        $message->parentMessage()->associate($message);
        if (!is_null($attachment))
            $message->attachments()->create(['path' => $attachment]);
        return $message;
    }

    /**
     * @return Builder
     */
    public function all()
    {
        return Message::whereSenderIs($this->model())->orWhereRecipientIs($this->model());
    }

    /**
     * @return MorphMany
     */
    public function sent()
    {
        return $this->model()->morphMany(Message::class, "sender");
    }

    /**
     * @return MorphMany
     */
    public function received()
    {
        return $this->model()->morphMany(Message::class, 'recipient');
    }
}