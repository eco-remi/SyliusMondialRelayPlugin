<?php

namespace Sherlockode\SyliusMondialRelayPlugin\Tests\Serializer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sherlockode\SyliusMondialRelayPlugin\Model\OpeningTimeSlot;
use Sherlockode\SyliusMondialRelayPlugin\Model\Point;
use Sherlockode\SyliusMondialRelayPlugin\Serializer\PointSerializer;
use Symfony\Contracts\Translation\TranslatorInterface;

class PointSerializerTest extends TestCase
{
    private TranslatorInterface&MockObject $translator;
    private PointSerializer $serializer;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->serializer = new PointSerializer($this->translator);
    }

    public function testSerialize(): void
    {
        $this->translator
            ->method('trans')
            ->willReturnCallback(function (string $id) {
                if ($id === 'sylius.mondial_relay.day.monday') {
                    return 'Lundi';
                }
                return $id;
            });

        $slot1 = new OpeningTimeSlot();
        $slot1->setDay(1);
        $slot1->setOpeningTime(new \DateTime('09:00'));
        $slot1->setClosingTime(new \DateTime('12:00'));

        $slot2 = new OpeningTimeSlot();
        $slot2->setDay(1);
        $slot2->setOpeningTime(new \DateTime('14:00'));
        $slot2->setClosingTime(new \DateTime('18:30'));

        $point = new Point();
        $point->setId('012345');
        $point->setName('Tabac Presse');
        $point->setStreet('10 Rue de la Paix');
        $point->setZipCode('75001');
        $point->setCity('Paris');
        $point->setCountry('FR');
        $point->setLatitude(48.8566);
        $point->setLongitude(2.3522);
        $point->setOpeningHours([$slot1, $slot2]);

        $serialized = $this->serializer->serialize($point);

        $this->assertSame([
            'id' => '012345',
            'label' => 'Tabac Presse',
            'address' => '10 Rue de la Paix',
            'zipCode' => '75001',
            'city' => 'Paris',
            'country' => 'FR',
            'lat' => 48.8566,
            'lng' => 2.3522,
            'businessHours' => [
                [
                    'day' => 1,
                    'label' => 'Lundi',
                    'slots' => [
                        [
                            'from' => '09h00',
                            'to' => '12h00',
                        ],
                        [
                            'from' => '14h00',
                            'to' => '18h30',
                        ],
                    ],
                ],
            ],
        ], $serialized);
    }
}
