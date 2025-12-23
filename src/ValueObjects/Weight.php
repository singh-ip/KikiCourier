<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Weight
{
	public function __construct(private float $kilograms)
	{
		if ($kilograms < 0) {
			throw new \InvalidArgumentException('Weight cannot be negative');
		}
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