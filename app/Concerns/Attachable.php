<?php


namespace Milebits\Society\Concerns;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait Attachable
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait Attachable
{
    /**
     * @return MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * @param string $path
     * @return Model|Attachment
     */
    public function addAttachment(string $path)
    {
        return $this->attachments()->create(['path' => $path]);
    }
}
