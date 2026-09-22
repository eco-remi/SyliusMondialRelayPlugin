<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Model;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Model\OpeningTimeSlot;
use EResponsable\SyliusMondialRelayPlugin\Model\Point;

class PointTest extends TestCase
{
    private Point $point;

    protected function setUp(): void
    {
        $this->point = new Point();
    }

    public function testGettersAndSetters(): void
    {
        $this->assertNull($this->point->getId());
        $this->assertSame($this->point, $this->point->setId('012345'));
        $this->assertSame('012345', $this->point->getId());

        $this->assertNull($this->point->getName());
        $this->assertSame($this->point, $this->point->setName('Relay Test'));
        $this->assertSame('Relay Test', $this->point->getName());

        $this->assertNull($this->point->getNameComplement());
        $this->assertSame($this->point, $this->point->setNameComplement('Batiment B'));
        $this->assertSame('Batiment B', $this->point->getNameComplement());

        $this->assertNull($this->point->getStreet());
        $this->assertSame($this->point, $this->point->setStreet('10 Rue de Paris'));
        $this->assertSame('10 Rue de Paris', $this->point->getStreet());

        $this->assertNull($this->point->getStreetComplement());
        $this->assertSame($this->point, $this->point->setStreetComplement('Etage 2'));
        $this->assertSame('Etage 2', $this->point->getStreetComplement());

        $this->assertNull($this->point->getZipCode());
        $this->assertSame($this->point, $this->point->setZipCode('75001'));
        $this->assertSame('75001', $this->point->getZipCode());

        $this->assertNull($this->point->getCity());
        $this->assertSame($this->point, $this->point->setCity('Paris'));
        $this->assertSame('Paris', $this->point->getCity());

        $this->assertNull($this->point->getCountry());
        $this->assertSame($this->point, $this->point->setCountry('FR'));
        $this->assertSame('FR', $this->point->getCountry());

        $this->assertNull($this->point->getLatitude());
        $this->assertSame($this->point, $this->point->setLatitude(48.8566));
        $this->assertSame(48.8566, $this->point->getLatitude());

        $this->assertNull($this->point->getLongitude());
        $this->assertSame($this->point, $this->point->setLongitude(2.3522));
        $this->assertSame(2.3522, $this->point->getLongitude());

        $this->assertNull($this->point->getActivityType());
        $this->assertSame($this->point, $this->point->setActivityType('001'));
        $this->assertSame('001', $this->point->getActivityType());

        $this->assertNull($this->point->getDistance());
        $this->assertSame($this->point, $this->point->setDistance(350));
        $this->assertSame(350, $this->point->getDistance());

        $this->assertNull($this->point->getLocalisation());
        $this->assertSame($this->point, $this->point->setLocalisation('Pres de la gare'));
        $this->assertSame('Pres de la gare', $this->point->getLocalisation());

        $this->assertNull($this->point->getLocalisationComplement());
        $this->assertSame($this->point, $this->point->setLocalisationComplement('Face au quai 3'));
        $this->assertSame('Face au quai 3', $this->point->getLocalisationComplement());

        $this->assertNull($this->point->getPlanUrl());
        $this->assertSame($this->point, $this->point->setPlanUrl('https://example.com/plan.jpg'));
        $this->assertSame('https://example.com/plan.jpg', $this->point->getPlanUrl());

        $this->assertNull($this->point->getPictureUrl());
        $this->assertSame($this->point, $this->point->setPictureUrl('https://example.com/photo.jpg'));
        $this->assertSame('https://example.com/photo.jpg', $this->point->getPictureUrl());

        $this->assertSame([], $this->point->getOpeningHours());
        $slot = new OpeningTimeSlot();
        $this->assertSame($this->point, $this->point->setOpeningHours([$slot]));
        $this->assertSame([$slot], $this->point->getOpeningHours());
    }

    public function testGetFullAddress(): void
    {
        $this->point->setStreet('10 Rue de Paris');
        $this->point->setStreetComplement('Batiment A');
        $this->point->setZipCode('75001');
        $this->point->setCity('Paris');

        $this->assertSame('10 Rue de Paris, Batiment A, 75001, Paris', $this->point->getFullAddress());
    }

    public function testGetFullAddressWithPartialData(): void
    {
        $this->point->setStreet('10 Rue de Paris');
        $this->point->setZipCode('75001');
        $this->point->setCity('Paris');

        $this->assertSame('10 Rue de Paris, 75001, Paris', $this->point->getFullAddress());
    }

    public function testGetShortAddress(): void
    {
        $this->point->setStreet(' 10 Rue de Paris ');
        $this->point->setStreetComplement(' Batiment A ');

        $this->assertSame('10 Rue de Paris, Batiment A', $this->point->getShortAddress());
    }
}
