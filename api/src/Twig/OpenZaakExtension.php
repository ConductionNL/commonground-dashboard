<?php

// src/Twig/CommongroundExtension.php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OpenZaakExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            // the logic of this filter is now implemented in a different class
        	new TwigFunction('openzaak_resource_list', [OpenZaakRuntime::class, 'getResourceList']),
            new TwigFunction('openzaak_resource', [OpenZaakRuntime::class, 'getResource']),
        	new TwigFunction('openzaak_component_list', [OpenZaakRuntime::class, 'getComponentList']),
        	new TwigFunction('openzaak_component_health', [OpenZaakRuntime::class, 'getComponentHealth']),
        	new TwigFunction('openzaak_component_resources', [OpenZaakRuntime::class, 'getComponentResources']),
        ];
    }
}
