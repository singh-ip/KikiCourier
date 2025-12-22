<?php

declare(strict_types=1);

namespace KikiCourier\Entity;

use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;

final class Package
{
    private PackageId $id;
    private Weight $weight;
    private Distance $distance;
    private string $offerCode;
    private ?Money $discount = null;
    private ?Money $totalCost = null;
    private ?float $estimatedDeliveryTime = null;

    public function __construct(
        PackageId $id,
        Weight $weight,
        Distance $distance,
        string $offerCode = ''
    ) {
        $this->id = $id;
        $this->weight = $weight;
        $this->distance = $distance;
        $this->offerCode = trim(strtoupper($offerCode));
    }

    public function getId(): PackageId
    {
        return $this->id;
    }

    public function getWeight(): Weight
    {
        return $this->weight;
    }

    public function getDistance(): Distance
    {
        return $this->distance;
    }

    public function getOfferCode(): string
    {
        return $this->offerCode;
    }

    public function hasOfferCode(): bool
    {
        return !empty($this->offerCode) && $this->offerCode !== 'NA';
    }

    public function setDiscount(Money $discount): void
    {
        $this->discount = $discount;
    }

    public function getDiscount(): Money
    {
        return $this->discount ?? new Money(0);
    }

    public function setTotalCost(Money $cost): void
    {
        $this->totalCost = $cost;
    }

    public function getTotalCost(): Money
    {
        if ($this->totalCost === null) {
            throw new \RuntimeException('Total cost not calculated yet');
        }
        return $this->totalCost;
    }

    public function setEstimatedDeliveryTime(float $hours): void
    {
        if ($hours < 0) {
            throw new \InvalidArgumentException('Delivery time cannot be negative');
        }
        $this->estimatedDeliveryTime = $hours;
    }

    public function getEstimatedDeliveryTime(): float
    {
        if ($this->estimatedDeliveryTime === null) {
            throw new \RuntimeException('Delivery time not calculated yet');
        }
        return $this->estimatedDeliveryTime;
    }

    public function hasEstimatedDeliveryTime(): bool
    {
        return $this->estimatedDeliveryTime !== null;
    }
}