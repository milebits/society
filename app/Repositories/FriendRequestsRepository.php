<?php


namespace Milebits\Society\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Milebits\Society\Models\FriendRequest;

class FriendRequestsRepository
{

    /**
     * @return Collection
     */
    public function all(): Collection
    {

    }

    /**
     * @return Collection
     */
    public function accepted(): Collection
    {

    }

    /**
     * @return Collection
     */
    public function blocked(): Collection
    {

    }

    /**
     * @return Collection
     */
    public function pending(): Collection
    {

    }

    /**
     * @return Collection
     */
    public function denied(): Collection
    {

    }

    /**
     * @param Model $model
     * @return Collection
     */
    public function mutualFriendsWith(Model $model): Collection
    {

    }

    /**
     * @param Model $friend
     * @return FriendRequest|null
     */
    public function addFriend(Model $friend): ?FriendRequest
    {

    }

    /**
     * @param Model $friend
     * @return bool
     */
    public function removeFriend(Model $friend): bool
    {

    }

    /**
     * @param Model $friend
     * @return bool
     */
    public function blockFriend(Model $friend): bool
    {

    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isFriendOf(Model $person): bool
    {

    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isBlockedBy(Model $person): bool
    {

    }

    /**
     * @param Model $person
     * @return bool
     */
    public function isDeniedBy(Model $person): bool
    {

    }

    /**
     * @param Model $person
     * @return bool
     */
    public function canBeFriendsWith(Model $person): bool
    {

    }

    /**
     * @return bool
     */
    public function isAllowedToSendFriendRequests(): bool
    {
        if(!(Auth::user() instanceof ("Milebits\Authorizer\Concerns\Authorizer"))) return false;
        return true;
    }
}