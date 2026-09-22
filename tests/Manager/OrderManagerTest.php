<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\Manager\OrderManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

class OrderManagerTest extends TestCase
{
    private OrderManager $orderManager;

    protected function setUp(): void
    {
        $this->orderManager = new OrderManager();
    }

    public function testGetOrderTotalWeightWithLowWeight(): void
    {
        $variant1 = $this->createMock(ProductVariantInterface::class);
        $variant1->method('getWeight')->willReturn(2.5);

        $variant2 = $this->createMock(ProductVariantInterface::class);
        $variant2->method('getWeight')->willReturn(3.0);

        $item1 = $this->createMock(OrderItemInterface::class);
        $item1->method('getVariant')->willReturn($variant1);

        $item2 = $this->createMock(OrderItemInterface::class);
        $item2->method('getVariant')->willReturn($variant2);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$item1, $item2]));

        $this->assertSame(5, $this->orderManager->getOrderTotalWeight($order));
    }

    public function testGetOrderTotalWeightCapsAt15(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $variant->method('getWeight')->willReturn(20.0);

        $item = $this->createMock(OrderItemInterface::class);
        $item->method('getVariant')->willReturn($variant);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$item]));

        $this->assertSame(15, $this->orderManager->getOrderTotalWeight($order));
    }

    public function testGetOrderTotalWeightWithoutVariant(): void
    {
        $item = $this->createMock(OrderItemInterface::class);
        $item->method('getVariant')->willReturn(null);

        $order = $this->createMock(OrderInterface::class);
        $order->method('getItems')->willReturn(new ArrayCollection([$item]));

        $this->assertSame(0, $this->orderManager->getOrderTotalWeight($order));
    }
}
