<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Speed
{
	public function __construct(private float $kmPerHour)
	{
		if ($kmPerHour <= 0) {
			throw new \InvalidArgumentException('Speed must be positive');
		}
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