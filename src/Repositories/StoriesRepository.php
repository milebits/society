<?php

namespace Milebits\Society\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Milebits\Society\Models\Story;

/**
 * Class StoriesRepository
 *
 * @package Milebits\Society\Repositories
 */
class StoriesRepository extends ChildRepository
{
    /**
     * @return MorphMany
     */
    public function all(): MorphMany
    {
        return $this->model()->morphMany(Story::class, "owner");
    }

    /**
     * @param string $filePath
     * @param string|null $content
     * @param bool $enabled
     *
     * @return \Illuminate\Database\Eloquent\Model|Story|null
     */
    public function add(string $filePath, string $content = null, bool $enabled = true): Model|Story|null
    {
        return $this->all()->create([
            'media_path' => $filePath,
            'content' => $content,
            'enabled' => $enabled,
        ]);
    }
}