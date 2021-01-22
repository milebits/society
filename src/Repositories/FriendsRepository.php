<?php


namespace Milebits\Society\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Milebits\Society\Concerns\Sociable;
use Milebits\Society\Models\FriendRequest;

class FriendsRepository extends ChildRepository
{
    /**
     * @return Builder
     */
    public function all(): Builder
    {
        return FriendRequest::whereSenderIs($this->model())->orWhereRecipientIs($this->model());
    }

    /**
     * @return Builder
     */
    public function accepted(): Builder
    {
        return FriendRequest::whereSenderIs($this->model())->orWhereRecipientIs($this->model())->accepted();
    }

    /**
     * @return Builder
     */
    public function blocked(): Builder
    {
        return FriendRequest::whereSenderIs($this->model())->orWhereRecipientIs($this->model())->blocked();
    }

    /**
     * @return Builder
     */
    public function pending(): Builder
    {
        return FriendRequest::whereSenderIs($this->model())->orWhereRecipientIs($this->model())->pending();
    }

    /**
     * @return Builder
     */
    public function denied(): Builder
    {
        return FriendRequest::whereSenderIs($this->model())->orWhereRecipientIs($this->model())->denied();
    }

    /**
     * @param Model|Sociable $model
     * @return Collection
     */
    public function mutual(Model $model): Collection
    {
        if ($model instanceof Sociable)
            return $model->society()->friends()->accepted()->get()->union($this->accepted()->get());
        return collect([]);
    }

    /**
     * @param Model $friend
     * @param string $status
     * @return FriendRequest|null
     * @throws Exception
     */
    public function newRequest(Model $friend, string $status): ?FriendRequest
    {
        if (!$this->delete($friend)) return null;
        return FriendRequest::create([
            'sender_id' => $this->model()->{$this->model()->getKeyName()},
            'sender_type' => $this->model()->getMorphClass(),
            'recipient_id' => $friend->{$friend->getKeyName()},
            'recipient_type' => $friend->getMorphClass(),
            'status' => $status,
        ]);
    }

    /**
     * @param Model $friend
     * @return FriendRequest|null
     * @throws Exception
     */
    public function add(Model $friend): ?FriendRequest
    {
        if (!$this->canAdd($friend)) return null;
        return $this->newRequest($friend, FriendRequest::PENDING);
    }

    /**
     * @param Model $friend
     * @return bool
     * @throws Exception
     */
    public function delete(Model $friend): bool
    {
        $friendRequest = FriendRequest::whereBetweenModels($this->model(), $friend)->first();
        if (is_null($friendRequest)) return true;
        return $friendRequest->delete();
    }

    /**
     * @param Model $friend
     * @return FriendRequest
     * @throws Exception
     */
    public function block(Model $friend): FriendRequest
    {
        return $this->newRequest($friend, FriendRequest::BLOCKED);
    }

    /**
     * @param Model $friend
     * @return FriendRequest
     * @throws Exception
     */
    public function accept(Model $friend): FriendRequest
    {
        return $this->newRequest($friend, FriendRequest::ACCEPTED);
    }

    /**
     * @param Model $friend
     * @return FriendRequest
     * @throws Exception
     */
    public function deny(Model $friend): FriendRequest
    {
        return $this->newRequest($friend, FriendRequest::DENIED);
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isFriendOf(Model $person): bool
    {
        return FriendRequest::whereBetweenModels($this->model(), $person)->accepted()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function hasAlreadySentTo(Model $person): bool
    {
        return FriendRequest::whereSenderIs($this->model())->whereRecipientIs($person)->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function didBlock(Model $person): bool
    {
        return FriendRequest::whereRecipientIs($person)->whereSenderIs($this->model())->blocked()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function didDeny(Model $person): bool
    {
        return FriendRequest::whereRecipientIs($person)->whereSenderIs($this->model())->denied()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isBlockedBy(Model $person): bool
    {
        return FriendRequest::whereSenderIs($person)->whereRecipientIs($this->model())->blocked()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isDeniedBy(Model $person): bool
    {
        return FriendRequest::whereSenderIs($person)->whereRecipientIs($this->model())->denied()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function canAdd(Model $person): bool
    {
        return !$this->isDeniedBy($person)
            && !$this->isBlockedBy($person)
            && !$this->isFriendOf($person);
    }
}
