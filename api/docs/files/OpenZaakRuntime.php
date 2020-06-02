<?php

// src/Twig/Commonground.php

namespace App\Twig;

use App\Service\ZgwService as OpenZaakService;
use Twig\Extension\RuntimeExtensionInterface;

class OpenZaakRuntime implements RuntimeExtensionInterface
{
	private $openZaakService;

    public function __construct(OpenZaakService $openZaakService)
    {
    	$this->openZaakService = $openZaakService;
    }

    public function getResource($resource)
    {
    	return $this->openZaakService->getResource($resource);
    }

    public function getResourceList($query)
    {
    	return $this->openZaakService->getResourceList($query);
    }

    public function getComponentList()
    {
    	return $this->openZaakService->getComponentList();
    }

    public function getComponentHealth($component)
    {
    	return $this->openZaakService->getComponentHealth($component);
    }

    public function getComponentResources($component)
    {
    	return $this->openZaakService->getComponentResources($component);
    }
}
