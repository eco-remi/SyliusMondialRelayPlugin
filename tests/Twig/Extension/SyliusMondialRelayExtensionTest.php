<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Twig\Extension;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Twig\Extension\SyliusMondialRelayExtension;
use EResponsable\SyliusMondialRelayPlugin\Twig\SyliusMondialRelayRuntime;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SyliusMondialRelayExtensionTest extends TestCase
{
    private SyliusMondialRelayExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new SyliusMondialRelayExtension();
    }

    public function testGetFilters(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
        $this->assertSame('mondial_relay_pickup_point', $filters[0]->getName());
        $this->assertSame([SyliusMondialRelayRuntime::class, 'getPickupPoint'], $filters[0]->getCallable());
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(3, $functions);

        $functionNames = array_map(fn (TwigFunction $function) => $function->getName(), $functions);
        $this->assertContains('get_map_provider', $functionNames);
        $this->assertContains('google_api_key', $functionNames);
        $this->assertContains('is_mondial_relay_ticket_printing_enable', $functionNames);

        $this->assertSame([SyliusMondialRelayRuntime::class, 'getMapProvider'], $functions[0]->getCallable());
        $this->assertSame([SyliusMondialRelayRuntime::class, 'getGoogleApiKey'], $functions[1]->getCallable());
        $this->assertSame([SyliusMondialRelayRuntime::class, 'isTicketPrintingEnable'], $functions[2]->getCallable());
    }
}
