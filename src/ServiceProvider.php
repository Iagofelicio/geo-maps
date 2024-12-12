<?php

namespace Iagofelicio\GeoMaps;

use Iagofelicio\GeoMaps\Tags\Map;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{

    protected $tags = [
        Map::class,
    ];

    public function bootAddon()
    {
        //
    }
}
