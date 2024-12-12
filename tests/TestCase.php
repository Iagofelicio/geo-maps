<?php

namespace Iagofelicio\GeoMaps\Tests;

use Iagofelicio\GeoMaps\ServiceProvider;
use Statamic\Testing\AddonTestCase;

abstract class TestCase extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;
}
