<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Manager;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Manager\MapProviderManager;

class MapProviderManagerTest extends TestCase
{
    public function testConstants(): void
    {
        $this->assertSame('google', MapProviderManager::MAP_PROVIDER_GOOGLE);
        $this->assertSame('open_street_map', MapProviderManager::MAP_PROVIDER_OSM);
    }
}
