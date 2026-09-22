<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\Model\PickupPointTrait;

class PickupPointTraitTest extends TestCase
{
    public function testPickupPointTrait(): void
    {
        $dummy = new class {
            use PickupPointTrait;
        };

        $this->assertNull($dummy->getPickupPointId());
        $this->assertSame($dummy, $dummy->setPickupPointId('987654'));
        $this->assertSame('987654', $dummy->getPickupPointId());
    }
}
