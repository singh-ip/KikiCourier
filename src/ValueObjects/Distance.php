<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Distance
{
    private float $kilometers;

    public function __construct(float $kilometers)
    {
        if ($kilometers < 0) {
            throw new \InvalidArgumentException('Distance cannot be negative');
        }
        
        $this->kilometers = $kilometers;
    }

    public function getKilometers(): float
    {
        return $this->kilometers;
    }
}