<?php


namespace Milebits\Society\Concerns;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Response;
use Milebits\Society\Models\Attachment;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function Milebits\LaravelStream\Helpers\videoStream;
use function Milebits\Society\Helpers\constVal;

/**
 * Trait Attachable
 * @package Milebits\Society\Concerns
 * @mixin Model
 */
trait Attachable
{
    public function initializeAttachable()
    {
        $this->mergeFillable([
            $this->getAttachableIdColumn(), $this->getAttachableTypeColumn(),
        ]);
    }

    /**
     * @return string
     */
    public function getAttachableIdColumn(): string
    {
        return sprintf("%s_id", $this->getAttachableMorph());
    }

    /**
     * @return string
     */
    public function getAttachableTypeColumn(): string
    {
        return sprintf("%s_type", $this->getAttachableMorph());
    }

    /**
     * @return string
     */
    public function getAttachableMorph(): string
    {
        return constVal($this, 'ATTACHABLE_MORPH', 'attachable');
    }

    /**
     * @return MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, $this->getAttachableMorph());
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
    public function streamAttachable()
    {
        return videoStream($this->path);
    }
}
