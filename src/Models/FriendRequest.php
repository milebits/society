<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Society\Scopes\BetweenModelsScopes;
use Milebits\Society\Scopes\RecipientScopes;
use Milebits\Society\Scopes\SenderScopes;
use Milebits\Society\Scopes\StatusScopes;
use Milebits\Society\Security\FriendRequestAvoidanceManager;

/**
 * Class FriendRequest
 * @package Milebits\Society\Models
 *
 * @property string $status
 */
class FriendRequest extends Model
{
    // Traits
    use HasFactory, SoftDeletes, StatusScopes, SoftDeletes;
    use SenderScopes, RecipientScopes, BetweenModelsScopes;

    // Security
    use FriendRequestAvoidanceManager;

    const PENDING = "pending";
    const ACCEPTED = "accepted";
    const DENIED = "denied";
    const BLOCKED = "blocked";
    const RECIPIENT_MORPH = "recipient";
    const SENDER_MORPH = "sender";

}
