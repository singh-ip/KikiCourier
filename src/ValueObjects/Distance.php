<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Distance
{
	public function __construct(private float $kilometers)
	{
		if ($kilometers < 0) {
			throw new \InvalidArgumentException('Distance cannot be negative');
		}
	}

    public function getKilometers(): float
    {
        return $this->kilometers;
    }
}