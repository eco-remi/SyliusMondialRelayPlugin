<?php

declare(strict_types=1);

namespace EResponsable\SyliusMondialRelayPlugin\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait PickupPointTrait
 */
trait PickupPointTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="pickup_point_id", type="string", nullable=true)
     */
    private ?string $pickupPointId = null;

    /**
     * @return string|null
     */
    public function getPickupPointId(): ?string
    {
        return $this->pickupPointId;
    }

    /**
     * @param string|null $pickupPointId
     *
     * @return $this
     */
    public function setPickupPointId(?string $pickupPointId): self
    {
        $this->pickupPointId = $pickupPointId;

        return $this;
    }
}
