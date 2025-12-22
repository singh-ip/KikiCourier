<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Weight
{
    private float $kilograms;

    public function __construct(float $kilograms)
    {
        if ($kilograms < 0) {
            throw new \InvalidArgumentException('Weight cannot be negative');
        }
        
        $this->kilograms = $kilograms;
    }

    public function getKilograms(): float
    {
        return $this->kilograms;
    }

    public function add(Weight $weight): Weight
    {
        return new Weight($this->kilograms + $weight->getKilograms());
    }

    public function isLessThanOrEqual(Weight $weight): bool
    {
        return $this->kilograms <= $weight->getKilograms();
    }
}