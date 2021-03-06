<?php

namespace Milebits\Society\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Milebits\Eloquent\Filters\Concerns\Enableable;
use Milebits\Eloquent\Filters\Concerns\Nameable;
use Milebits\Society\Concerns\Leanable;

class Tag extends Model
{
    use SoftDeletes, HasFactory, Nameable, Enableable, Leanable;

    const TAGGABLE_COLUMN = "taggable";

    /**
     * @param string $class
     * @return MorphToMany
     */
    public function taggableOf(string $class)
    {
        return $this->morphedByMany($class, self::TAGGABLE_COLUMN);
    }
}
