<?php


namespace Milebits\Society\Concerns;

use Milebits\Society\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Trait Taggable
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait Taggable
{
    /**
     * @return MorphToMany
     */
    public function tags()
    {
        return $this->morphedByMany(Tag::class, Tag::TAGGABLE_COLUMN);
    }
}
