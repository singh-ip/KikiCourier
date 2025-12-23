<?php

declare(strict_types=1);

namespace KikiCourier\Entity;

use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Speed;

final class Vehicle
{
    private float $availableAt;

    public function __construct(private string $id, private Weight $maxWeight, private Speed $speed)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException('Vehicle ID cannot be empty');
        }
        $this->availableAt = 0.0;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMaxWeight(): Weight
    {
        return $this->maxWeight;
    }

    public function getSpeed(): Speed
    {
        return $this->speed;
    }

    public function getAvailableAt(): float
    {
        return $this->availableAt;
    }

    public function setAvailableAt(float $time): void
    {
        if ($time < 0) {
            throw new \InvalidArgumentException('Time cannot be negative');
        }
        $this->availableAt = $time;
    }
}