<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Society\Concerns\Attachable;
use Milebits\Society\Scopes\BetweenModelsScopes;
use Milebits\Society\Scopes\RecipientScopes;
use Milebits\Society\Scopes\SenderScopes;

class Message extends Model
{
    use Attachable, SoftDeletes, HasFactory;
    use SenderScopes, RecipientScopes, BetweenModelsScopes;

    /**
     * @return BelongsTo
     */
    public function parentMessage()
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return MorphTo
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function recipient()
    {
        return $this->morphTo();
    }
}