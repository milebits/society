<?php


namespace Milebits\Society\Concerns;

use App\Models\Lean;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Leanable
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait Leanable
{
    /**
     * @return MorphMany
     */
    public function leans()
    {
        return $this->morphMany(Lean::class, 'leanable');
    }
}
