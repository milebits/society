<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Eloquent\Filters\Concerns\Enableable;
use Milebits\Eloquent\Filters\Concerns\Sluggable;

class Attachment extends Model
{
    use SoftDeletes, HasFactory, Sluggable, Enableable;

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
