<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Society\Concerns\Commentable;
use Milebits\Society\Concerns\Leanable;
use Milebits\Society\Scopes\OwnerScopes;

class Comment extends Model
{
    use SoftDeletes, HasFactory, Commentable, Leanable, OwnerScopes;

    protected $fillable = [
        'commentable_id', 'commentable_type',
        'content',
        'attachment_id', 'attachment_type',
    ];

    /**
     * @return MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * @return MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
