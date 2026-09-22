<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\MondialRelay\Api\Factory;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\Api\Factory\PointFactory;

class PointFactoryTest extends TestCase
{
    private PointFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new PointFactory();
    }

    public function testCreate(): void
    {
        $raw = (object) [
            'Num' => '012345',
            'LgAdr1' => 'PRESSE DU CENTRE',
            'LgAdr2' => 'GALERIE MARCHANDE',
            'LgAdr3' => '15 RUE DE LA REPUBLIQUE',
            'LgAdr4' => 'BATIMENT A',
            'CP' => '75001',
            'Ville' => 'PARIS',
            'Pays' => 'FR',
            'TypeActivite' => '001',
            'Distance' => 450,
            'Localisation1' => 'CENTRE VILLE',
            'Localisation2' => 'FACE MAIRIE',
            'URL_Plan' => 'https://example.com/plan',
            'URL_Photo' => 'https://example.com/photo',
            'Latitude' => '48,8566',
            'Longitude' => '2,3522',
            'Horaires_Lundi' => (object) [
                'string' => ['0800', '1200', '1400', '1800'],
            ],
            'Horaires_Mardi' => (object) [
                'string' => ['0900', '1900', '0000', '0000'],
            ],
            'Horaires_Dimanche' => (object) [
                'string' => ['0000', '0000', '0000', '0000'],
            ],
        ];

        $point = $this->factory->create($raw);

        $this->assertSame('012345', $point->getId());
        $this->assertSame('PRESSE DU CENTRE', $point->getName());
        $this->assertSame('GALERIE MARCHANDE', $point->getNameComplement());
        $this->assertSame('15 RUE DE LA REPUBLIQUE', $point->getStreet());
        $this->assertSame('BATIMENT A', $point->getStreetComplement());
        $this->assertSame('75001', $point->getZipCode());
        $this->assertSame('PARIS', $point->getCity());
        $this->assertSame('FR', $point->getCountry());
        $this->assertSame('001', $point->getActivityType());
        $this->assertSame(450, $point->getDistance());
        $this->assertSame('CENTRE VILLE', $point->getLocalisation());
        $this->assertSame('FACE MAIRIE', $point->getLocalisationComplement());
        $this->assertSame('https://example.com/plan', $point->getPlanUrl());
        $this->assertSame('https://example.com/photo', $point->getPictureUrl());
        $this->assertEqualsWithDelta(48.8566, $point->getLatitude(), 0.0001);
        $this->assertEqualsWithDelta(2.3522, $point->getLongitude(), 0.0001);

        $openingHours = $point->getOpeningHours();
        $this->assertCount(3, $openingHours);

        // Lundi slots
        $this->assertSame(1, $openingHours[0]->getDay());
        $this->assertSame('08:00', $openingHours[0]->getOpeningTime()->format('H:i'));
        $this->assertSame('12:00', $openingHours[0]->getClosingTime()->format('H:i'));

        $this->assertSame(1, $openingHours[1]->getDay());
        $this->assertSame('14:00', $openingHours[1]->getOpeningTime()->format('H:i'));
        $this->assertSame('18:00', $openingHours[1]->getClosingTime()->format('H:i'));

        // Mardi slot
        $this->assertSame(2, $openingHours[2]->getDay());
        $this->assertSame('09:00', $openingHours[2]->getOpeningTime()->format('H:i'));
        $this->assertSame('19:00', $openingHours[2]->getClosingTime()->format('H:i'));
    }

    public function testCreateWithEmptyRaw(): void
    {
        $raw = (object) [];
        $point = $this->factory->create($raw);

        $this->assertNull($point->getId());
        $this->assertNull($point->getName());
        $this->assertNull($point->getLatitude());
        $this->assertNull($point->getLongitude());
        $this->assertSame([], $point->getOpeningHours());
    }
}
