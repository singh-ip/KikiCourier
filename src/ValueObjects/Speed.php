<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Speed
{
    private float $kmPerHour;

    public function __construct(float $kmPerHour)
    {
        if ($kmPerHour <= 0) {
            throw new \InvalidArgumentException('Speed must be positive');
        }
        
        $this->kmPerHour = $kmPerHour;
    }

    public function getKmPerHour(): float
    {
        return $this->kmPerHour;
    }

    public function calculateTime(Distance $distance): float
    {
        return $distance->getKilometers() / $this->kmPerHour;
    }
}