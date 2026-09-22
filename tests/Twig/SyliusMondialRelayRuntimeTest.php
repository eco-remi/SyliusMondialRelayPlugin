<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Twig;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Model\Point;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\Api\Exception\ApiException;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\MondialRelay;
use EResponsable\SyliusMondialRelayPlugin\Twig\SyliusMondialRelayRuntime;
use Sylius\Component\Core\Model\Shipment;

class SyliusMondialRelayRuntimeTest extends TestCase
{
    private MondialRelay&MockObject $apiClient;
    private SyliusMondialRelayRuntime $runtime;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(MondialRelay::class);
        $this->runtime = new SyliusMondialRelayRuntime(
            $this->apiClient,
            true,
            'google',
            'MY_GOOGLE_KEY'
        );
    }

    public function testGetters(): void
    {
        $this->assertSame('google', $this->runtime->getMapProvider());
        $this->assertSame('MY_GOOGLE_KEY', $this->runtime->getGoogleApiKey());
        $this->assertTrue($this->runtime->isTicketPrintingEnable());
    }

    public function testGetPickupPointSuccess(): void
    {
        /** @var Shipment&MockObject $shipment */
        $shipment = $this->getMockBuilder(Shipment::class)
            ->addMethods(['getPickupPointId'])
            ->getMock();
        $shipment->method('getPickupPointId')->willReturn('012345');

        $point = new Point();
        $this->apiClient
            ->expects($this->once())
            ->method('getPickupPoint')
            ->with('012345')
            ->willReturn($point);

        $result = $this->runtime->getPickupPoint($shipment);
        $this->assertSame($point, $result);
    }

    public function testGetPickupPointWithoutPickupPointId(): void
    {
        /** @var Shipment&MockObject $shipment */
        $shipment = $this->getMockBuilder(Shipment::class)
            ->addMethods(['getPickupPointId'])
            ->getMock();
        $shipment->method('getPickupPointId')->willReturn(null);

        $this->apiClient->expects($this->never())->method('getPickupPoint');

        $result = $this->runtime->getPickupPoint($shipment);
        $this->assertNull($result);
    }

    public function testGetPickupPointHandlesExceptionGracefully(): void
    {
        /** @var Shipment&MockObject $shipment */
        $shipment = $this->getMockBuilder(Shipment::class)
            ->addMethods(['getPickupPointId'])
            ->getMock();
        $shipment->method('getPickupPointId')->willReturn('012345');

        $this->apiClient
            ->expects($this->once())
            ->method('getPickupPoint')
            ->willThrowException(new ApiException(400, 'API error'));

        $result = $this->runtime->getPickupPoint($shipment);
        $this->assertNull($result);
    }
}
