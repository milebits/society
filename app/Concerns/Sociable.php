<?php


namespace Milebits\Society\Concerns;


use Milebits\Society\Repositories\SocietyRepository;

/**
 * Trait Sociable
 * @package Milebits\Society\Concerns
 */
trait Sociable
{
    protected ?SocietyRepository $society = null;

    public function initializeSociable()
    {
        if (is_null($this->society))
            $this->society = new SocietyRepository($this);
    }

    /**
     * @return SocietyRepository|null
     */
    public function society()
    {
        return $this->society;
    }
}
