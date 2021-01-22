<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Eloquent\Filters\Concerns\Enableable;
use Milebits\Eloquent\Filters\Concerns\Sluggable;
use Milebits\Society\Concerns\Commentable;
use Milebits\Society\Concerns\Leanable;
use Milebits\Society\Concerns\Taggable;

class Attachment extends Model
{
    use SoftDeletes, HasFactory, Sluggable, Enableable, Taggable, Leanable, Commentable;

    protected $fillable = ['path'];

    public bool $AUTO_SLUG = true;

    /**
     * @return MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }
}
