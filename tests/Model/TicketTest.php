<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\Model\Ticket;

class TicketTest extends TestCase
{
    private Ticket $ticket;

    protected function setUp(): void
    {
        $this->ticket = new Ticket();
    }

    public function testGettersAndSetters(): void
    {
        $this->assertNull($this->ticket->getShippingNumber());
        $this->assertSame($this->ticket, $this->ticket->setShippingNumber('EXP123456'));
        $this->assertSame('EXP123456', $this->ticket->getShippingNumber());

        $this->assertNull($this->ticket->getBaseUrl());
        $this->assertSame($this->ticket, $this->ticket->setBaseUrl('https://api.mondialrelay.com'));
        $this->assertSame('https://api.mondialrelay.com', $this->ticket->getBaseUrl());

        $this->assertNull($this->ticket->getPath());
        $this->assertSame($this->ticket, $this->ticket->setPath('etiquette.aspx'));
        $this->assertSame('etiquette.aspx', $this->ticket->getPath());

        $this->assertSame([], $this->ticket->getQueryString());
        $this->assertSame($this->ticket, $this->ticket->setQueryString(['ens' => 'BDTEST', 'exp' => '123456']));
        $this->assertSame(['ens' => 'BDTEST', 'exp' => '123456'], $this->ticket->getQueryString());
    }

    public function testGenerateTicketUrls(): void
    {
        $this->ticket->setBaseUrl('https://api.mondialrelay.com');
        $this->ticket->setPath('etiquette.aspx');
        $this->ticket->setQueryString(['ens' => 'BDTEST', 'exp' => '123456']);

        $this->assertSame(
            'https://api.mondialrelay.com/etiquette.aspx?ens=BDTEST&exp=123456&format=A4',
            $this->ticket->getA4TicketUrl()
        );

        $this->assertSame(
            'https://api.mondialrelay.com/etiquette.aspx?ens=BDTEST&exp=123456&format=A5',
            $this->ticket->getA5TicketUrl()
        );

        $this->assertSame(
            'https://api.mondialrelay.com/etiquette.aspx?ens=BDTEST&exp=123456&format=10x15',
            $this->ticket->get10x15TicketUrl()
        );
    }
}
