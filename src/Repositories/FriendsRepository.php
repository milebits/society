<?php


namespace Milebits\Society\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
    public function mutualFriendsWith(Model $model): Collection
    {
        return $model->society()->friends()->accepted()->get()->union($this->accepted()->get());
    }

    /**
     * @param Model $friend
     * @param string $status
     * @return FriendRequest
     */
    public function newRequest(Model $friend, string $status): ?FriendRequest
    {
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
     */
    public function add(Model $friend): ?FriendRequest
    {
        if (!$this->canBeFriendsWith($friend)) return null;
        return $this->newRequest($friend, FriendRequest::PENDING);
    }

    /**
     * @param Model $friend
     * @return bool
     * @throws Exception
     */
    public function remove(Model $friend): bool
    {
        return FriendRequest::whereBetweenModels($this->model(), $friend)->first()->delete();
    }

    /**
     * @param Model $friend
     * @return bool
     */
    public function block(Model $friend): bool
    {
        $friendRequest = FriendRequest::whereBetweenModels($this->model(), $friend)->first();
        $friendRequest = $friendRequest ?? $this->newRequest($friend, FriendRequest::BLOCKED);
        $friendRequest->status = FriendRequest::BLOCKED;
        return $friendRequest->save();
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
    public function isBlockedBy(Model $person): bool
    {
        return FriendRequest::whereBetweenModels($this->model(), $person)->blocked()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isDeniedBy(Model $person): bool
    {
        return FriendRequest::whereBetweenModels($this->model(), $person)->denied()->exists();
    }

    /**
     * @param Model $person
     * @return bool
     */
    public function canBeFriendsWith(Model $person): bool
    {
        return $this->isAllowedToSendFriendRequests()
            && !$this->isDeniedBy($person)
            && !$this->isBlockedBy($person)
            && !$this->isFriendOf($person);
    }

    /**
     * @return bool
     */
    public function isAllowedToSendFriendRequests(): bool
    {
        if (!(Auth::user() instanceof ("Milebits\Authorizer\Concerns\Authorizer"))) return false;
        return true;
    }
}
