<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\PlaceFinder;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\PlaceFinder\PlaceFinderInterface;
use Sherlockode\SyliusMondialRelayPlugin\PlaceFinder\PlaceFinderRegistry;

class PlaceFinderRegistryTest extends TestCase
{
    public function testGetFinderFound(): void
    {
        $finder1 = $this->createMock(PlaceFinderInterface::class);
        $finder1->method('supports')->with('google')->willReturn(true);

        $finder2 = $this->createMock(PlaceFinderInterface::class);
        $finder2->method('supports')->with('google')->willReturn(false);

        $registry = new PlaceFinderRegistry([$finder1, $finder2]);

        $this->assertSame($finder1, $registry->getFinder('google'));
    }

    public function testGetFinderNotFound(): void
    {
        $finder = $this->createMock(PlaceFinderInterface::class);
        $finder->method('supports')->with('unknown')->willReturn(false);

        $registry = new PlaceFinderRegistry([$finder]);

        $this->assertNull($registry->getFinder('unknown'));
    }
}
