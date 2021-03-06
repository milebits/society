<?php


namespace Milebits\Society\Services;


abstract class ChildService
{
    /**
     * @var SocietyService|null
     */
    protected ?SocietyService $societyService = null;

    /**
     * ChildService constructor.
     * @param SocietyService $society
     */
    public function __construct(SocietyService $society)
    {
        $this->societyService = $society;
    }

    /**
     * @return SocietyService|null
     */
    public function societyService(): ?SocietyService
    {
        return $this->societyService;
    }

    /**
     * @param string $service
     * @return ChildService|null
     */
    public function otherService(string $service)
    {
        return $this->societyService()->buildService($service);
    }
}