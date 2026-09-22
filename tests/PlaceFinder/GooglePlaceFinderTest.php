<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\PlaceFinder;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\Manager\MapProviderManager;
use Sherlockode\SyliusMondialRelayPlugin\PlaceFinder\GooglePlaceFinder;

class GooglePlaceFinderTest extends TestCase
{
    public function testSupports(): void
    {
        $finder = new GooglePlaceFinder('fake_api_key');

        $this->assertTrue($finder->supports(MapProviderManager::MAP_PROVIDER_GOOGLE));
        $this->assertFalse($finder->supports(MapProviderManager::MAP_PROVIDER_OSM));
        $this->assertFalse($finder->supports(null));
        $this->assertFalse($finder->supports('unknown'));
    }
}
