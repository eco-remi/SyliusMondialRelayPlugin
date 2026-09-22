<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\MondialRelay\Api\Factory;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\MondialRelay\Api\Factory\TicketFactory;

class TicketFactoryTest extends TestCase
{
    private TicketFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new TicketFactory('https://api.mondialrelay.com');
    }

    public function testCreate(): void
    {
        $raw = (object) [
            'ExpeditionNum' => '12345678',
            'URL_Etiquette' => 'https://api.mondialrelay.com/ticket.aspx?ens=BDTEST&exp=12345678&crc=ABCDEF',
        ];

        $ticket = $this->factory->create($raw);

        $this->assertSame('12345678', $ticket->getShippingNumber());
        $this->assertSame('https://api.mondialrelay.com', $ticket->getBaseUrl());
        $this->assertSame('/ticket.aspx', $ticket->getPath());
        $this->assertSame([
            'ens' => 'BDTEST',
            'exp' => '12345678',
            'crc' => 'ABCDEF',
        ], $ticket->getQueryString());
    }
}
