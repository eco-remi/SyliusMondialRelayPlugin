<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\MondialRelay;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Model\Point;
use EResponsable\SyliusMondialRelayPlugin\Model\Ticket;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\Api\Client;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\MondialRelay;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\AddressInterface as CoreAddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

class MondialRelayTest extends TestCase
{
    private Client&MockObject $client;
    private MondialRelay $mondialRelay;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->mondialRelay = new MondialRelay($this->client);
    }

    public function testGetPickupPoint(): void
    {
        $point = new Point();
        $point->setId('012345');

        $this->client
            ->expects($this->once())
            ->method('WSI4PointRelaisRecherche')
            ->with([
                'NumPointRelais' => '012345',
                'Pays' => 'FR',
            ])
            ->willReturn([$point]);

        $result = $this->mondialRelay->getPickupPoint('012345', 'FR');
        $this->assertSame($point, $result);
    }

    public function testGetPickupPointReturnsNullWhenNotFound(): void
    {
        $this->client
            ->expects($this->once())
            ->method('WSI4PointRelaisRecherche')
            ->willReturn([]);

        $result = $this->mondialRelay->getPickupPoint('999999', 'FR');
        $this->assertNull($result);
    }

    public function testFindPickupPointsAround(): void
    {
        $address = $this->createMock(AddressInterface::class);
        $address->method('getCountryCode')->willReturn('FR');
        $address->method('getCity')->willReturn('Paris');
        $address->method('getPostcode')->willReturn('75001');

        $point = new Point();

        $this->client
            ->expects($this->once())
            ->method('WSI4PointRelaisRecherche')
            ->with([
                'Pays' => 'FR',
                'Ville' => 'Paris',
                'CP' => '75001',
                'DelaiEnvoi' => '0',
                'RayonRecherche' => '20',
                'NombreResultats' => '30',
            ])
            ->willReturn([$point]);

        $result = $this->mondialRelay->findPickupPointsAround($address);
        $this->assertSame([$point], $result);
    }

    public function testFindPickupPointsByZipCode(): void
    {
        $point = new Point();

        $this->client
            ->expects($this->once())
            ->method('WSI4PointRelaisRecherche')
            ->with([
                'Pays' => 'FR',
                'CP' => '69001',
                'Action' => 'REL',
                'DelaiEnvoi' => '0',
                'RayonRecherche' => '20',
                'NombreResultats' => '30',
            ])
            ->willReturn([$point]);

        $result = $this->mondialRelay->findPickupPointsByZipCode('69001', 'REL', 'FR');
        $this->assertSame([$point], $result);
    }

    public function testPrintTicket(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getGender')->willReturn(CustomerInterface::FEMALE_GENDER);
        $customer->method('getId')->willReturn(42);
        $customer->method('getPhoneNumber')->willReturn('0612345678');
        $customer->method('getEmail')->willReturn('client@example.com');

        $shippingAddress = $this->createMock(CoreAddressInterface::class);
        $shippingAddress->method('getLastName')->willReturn('Dupont');
        $shippingAddress->method('getFirstName')->willReturn('Marie');
        $shippingAddress->method('getCountryCode')->willReturn('FR');

        $billingAddress = $this->createMock(CoreAddressInterface::class);
        $billingAddress->method('getStreet')->willReturn('10 Rue de la Paix');
        $billingAddress->method('getCity')->willReturn('Paris');
        $billingAddress->method('getPostcode')->willReturn('75001');
        $billingAddress->method('getCountryCode')->willReturn('FR');

        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);
        $shopBillingData->method('getCompany')->willReturn('My Store SAS');
        $shopBillingData->method('getStreet')->willReturn('5 Avenue des Champs');
        $shopBillingData->method('getCity')->willReturn('Paris');
        $shopBillingData->method('getPostcode')->willReturn('75008');
        $shopBillingData->method('getCountryCode')->willReturn('FR');

        $locale = $this->createMock(LocaleInterface::class);
        $locale->method('getCode')->willReturn('fr_FR');

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getShopBillingData')->willReturn($shopBillingData);
        $channel->method('getDefaultLocale')->willReturn($locale);
        $channel->method('getContactPhoneNumber')->willReturn('0123456789');
        $channel->method('getContactEmail')->willReturn('contact@mystore.fr');

        $order = $this->createMock(OrderInterface::class);
        $order->method('getCustomer')->willReturn($customer);
        $order->method('getShippingAddress')->willReturn($shippingAddress);
        $order->method('getBillingAddress')->willReturn($billingAddress);
        $order->method('getChannel')->willReturn($channel);
        $order->method('getNumber')->willReturn('000001');
        $order->method('getLocaleCode')->willReturn('fr_FR');
        $order->method('getCurrencyCode')->willReturn('EUR');
        $order->method('getItemsTotal')->willReturn(5000);

        /** @var ShipmentInterface&MockObject $shipment */
        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->addMethods(['getPickupPointId'])
            ->getMockForAbstractClass();
        $shipment->method('getOrder')->willReturn($order);
        $shipment->method('getPickupPointId')->willReturn('012345');

        $ticket = new Ticket();

        $this->client
            ->expects($this->once())
            ->method('WSI2CreationEtiquette')
            ->with($this->callback(function (array $payload) {
                return $payload['ModeCol'] === 'REL'
                    && $payload['ModeLiv'] === '24R'
                    && $payload['NDossier'] === '000001'
                    && $payload['NClient'] === 42
                    && $payload['Expe_Langage'] === 'FR'
                    && $payload['Expe_Ad1'] === 'My Store SAS'
                    && $payload['Expe_Ad3'] === '5 Avenue des Champs'
                    && $payload['Expe_Ville'] === 'Paris'
                    && $payload['Expe_CP'] === '75008'
                    && $payload['Expe_Pays'] === 'FR'
                    && $payload['Expe_Tel1'] === '0123456789'
                    && $payload['Expe_Mail'] === 'contact@mystore.fr'
                    && $payload['Dest_Langage'] === 'FR'
                    && $payload['Dest_Ad1'] === 'MME Dupont Marie'
                    && $payload['Dest_Ad3'] === '10 Rue de la Paix'
                    && $payload['Dest_Ville'] === 'Paris'
                    && $payload['Dest_CP'] === '75001'
                    && $payload['Dest_Pays'] === 'FR'
                    && $payload['Dest_Tel1'] === '0612345678'
                    && $payload['Dest_Mail'] === 'client@example.com'
                    && $payload['Poids'] === 1500
                    && $payload['NbColis'] === 2
                    && $payload['CRT_Devise'] === 'EUR'
                    && $payload['Exp_Valeur'] === 5000
                    && $payload['COL_Rel'] === '012345'
                    && $payload['LIV_Rel'] === '012345';
            }))
            ->willReturn($ticket);

        $userData = [
            'collectionMode' => 'REL',
            'deliveryMode' => '24R',
            'weight' => 1500,
            'parcelCount' => 2,
        ];

        $result = $this->mondialRelay->printTicket($shipment, $userData);
        $this->assertSame($ticket, $result);
    }
}
