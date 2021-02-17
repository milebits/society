<?php

namespace Milebits\Society\Services;

class SocietyService
{
    /**
     * @param string $service
     * @return ChildService|null
     */
    public function buildService(string $service): ?ChildService
    {
        return is_null($class = config('society.services.' . $service)) ? null : new $class($this);
    }

    /**
     * @param string $service
     * @return ChildService|null
     */
    public static function service(string $service): ?ChildService
    {
        return (new static())->buildService($service);
    }
}