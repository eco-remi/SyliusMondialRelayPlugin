<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private Processor $processor;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    public function testProcessConfigurationWithDefaults(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'wsdl' => 'https://api.mondialrelay.com/Web_Services.asmx?WSDL',
                'merchant_id' => 'BDTEST13',
                'private_key' => 'Secret123',
            ],
        ]);

        $this->assertSame([
            'wsdl' => 'https://api.mondialrelay.com/Web_Services.asmx?WSDL',
            'merchant_id' => 'BDTEST13',
            'private_key' => 'Secret123',
            'map_provider' => null,
            'google_api_key' => null,
            'mondial_relay_base_url' => 'https://www.mondialrelay.com',
            'enable_ticket_printing' => true,
        ], $config);
    }

    public function testProcessConfigurationWithCustomValues(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            [
                'wsdl' => 'https://api.mondialrelay.com/Web_Services.asmx?WSDL',
                'merchant_id' => 'BDTEST13',
                'private_key' => 'Secret123',
                'map_provider' => 'google',
                'google_api_key' => 'AIzaSy123456',
                'mondial_relay_base_url' => 'https://connect.mondialrelay.com',
                'enable_ticket_printing' => false,
            ],
        ]);

        $this->assertSame([
            'wsdl' => 'https://api.mondialrelay.com/Web_Services.asmx?WSDL',
            'merchant_id' => 'BDTEST13',
            'private_key' => 'Secret123',
            'map_provider' => 'google',
            'google_api_key' => 'AIzaSy123456',
            'mondial_relay_base_url' => 'https://connect.mondialrelay.com',
            'enable_ticket_printing' => false,
        ], $config);
    }
}
