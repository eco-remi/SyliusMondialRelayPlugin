<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Model\OpeningTimeSlot;

class OpeningTimeSlotTest extends TestCase
{
    private OpeningTimeSlot $slot;

    protected function setUp(): void
    {
        $this->slot = new OpeningTimeSlot();
    }

    public function testGettersAndSetters(): void
    {
        $this->assertNull($this->slot->getDay());
        $this->assertSame($this->slot, $this->slot->setDay(1));
        $this->assertSame(1, $this->slot->getDay());

        $openingTime = new \DateTime('08:30:00');
        $this->assertNull($this->slot->getOpeningTime());
        $this->assertSame($this->slot, $this->slot->setOpeningTime($openingTime));
        $this->assertSame($openingTime, $this->slot->getOpeningTime());

        $closingTime = new \DateTime('19:00:00');
        $this->assertNull($this->slot->getClosingTime());
        $this->assertSame($this->slot, $this->slot->setClosingTime($closingTime));
        $this->assertSame($closingTime, $this->slot->getClosingTime());
    }

    /**
     * @dataProvider dayLabelProvider
     */
    public function testGetDayLabel(?int $day, ?string $expectedLabel): void
    {
        $this->slot->setDay($day);
        $this->assertSame($expectedLabel, $this->slot->getDayLabel());
    }

    public static function dayLabelProvider(): array
    {
        return [
            [null, null],
            [0, null],
            [1, 'monday'],
            [2, 'tuesday'],
            [3, 'wednesday'],
            [4, 'thursday'],
            [5, 'friday'],
            [6, 'saturday'],
            [7, 'sunday'],
            [8, null],
        ];
    }
}
