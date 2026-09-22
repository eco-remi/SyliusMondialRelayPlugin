<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\MondialRelay\Api\Factory;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\MondialRelay\Api\Factory\OpeningTimeSlotFactory;

class OpeningTimeSlotFactoryTest extends TestCase
{
    private OpeningTimeSlotFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new OpeningTimeSlotFactory();
    }

    public function testCreate(): void
    {
        $timeSlot = $this->factory->create(1, '0830', '1900');

        $this->assertSame(1, $timeSlot->getDay());
        $this->assertSame('monday', $timeSlot->getDayLabel());
        $this->assertNotNull($timeSlot->getOpeningTime());
        $this->assertSame('08:30', $timeSlot->getOpeningTime()->format('H:i'));
        $this->assertNotNull($timeSlot->getClosingTime());
        $this->assertSame('19:00', $timeSlot->getClosingTime()->format('H:i'));
    }
}
