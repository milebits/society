<?php


namespace Milebits\Society\Concerns;

use Milebits\Society\Models\Attachment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function Milebits\LaravelStream\Helpers\videoStream;

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

    /**
     * @return Application|ResponseFactory|Response|StreamedResponse
     */
    public function stream()
    {
        return videoStream($this->path);
    }
}
