<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Calculator;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Calculator\MondialRelayCalculator;
use Sylius\Component\Shipping\Model\ShipmentInterface;

class MondialRelayCalculatorTest extends TestCase
{
    private MondialRelayCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new MondialRelayCalculator();
    }

    public function testGetType(): void
    {
        $this->assertSame('mondial_relay', $this->calculator->getType());
        $this->assertSame(MondialRelayCalculator::TYPE_MONDIAL_RELAY, $this->calculator->getType());
    }

    public function testCalculateWithoutRangesReturnsZero(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->assertSame(0, $this->calculator->calculate($shipment, []));
        $this->assertSame(0, $this->calculator->calculate($shipment, ['ranges' => []]));
    }

    public function testCalculateWithMinAndMaxWeight(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getShippingWeight')->willReturn(500.0);

        $configuration = [
            'ranges' => [
                [
                    'minWeight' => 0,
                    'maxWeight' => 250,
                    'amount' => 450,
                ],
                [
                    'minWeight' => 251,
                    'maxWeight' => 1000,
                    'amount' => 690,
                ],
                [
                    'minWeight' => 1001,
                    'maxWeight' => 2000,
                    'amount' => 890,
                ],
            ],
        ];

        $this->assertSame(690, $this->calculator->calculate($shipment, $configuration));
    }

    public function testCalculateWithOnlyMinWeight(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getShippingWeight')->willReturn(1500.0);

        $configuration = [
            'ranges' => [
                [
                    'minWeight' => 1000,
                    'maxWeight' => null,
                    'amount' => 1200,
                ],
            ],
        ];

        $this->assertSame(1200, $this->calculator->calculate($shipment, $configuration));
    }

    public function testCalculateWithOnlyMaxWeight(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getShippingWeight')->willReturn(300.0);

        $configuration = [
            'ranges' => [
                [
                    'minWeight' => null,
                    'maxWeight' => 500,
                    'amount' => 550,
                ],
            ],
        ];

        $this->assertSame(550, $this->calculator->calculate($shipment, $configuration));
    }

    public function testCalculateWhenWeightDoesNotMatchAnyRangeReturnsZero(): void
    {
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getShippingWeight')->willReturn(3000.0);

        $configuration = [
            'ranges' => [
                [
                    'minWeight' => 0,
                    'maxWeight' => 1000,
                    'amount' => 500,
                ],
            ],
        ];

        $this->assertSame(0, $this->calculator->calculate($shipment, $configuration));
    }
}
