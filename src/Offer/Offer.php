<?php

declare(strict_types=1);

namespace KikiCourier\Offer;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\Money;

final class Offer implements OfferInterface
{
	public function __construct(
		private string $code,
		private float $discountPercentage,
		private float $minDistance,
		private float $maxDistance,
		private float $minWeight,
		private float $maxWeight
	) {
		if (empty($code)) {
			throw new \InvalidArgumentException('Offer code cannot be empty');
		}
		if ($discountPercentage < 0 || $discountPercentage > 100) {
			throw new \InvalidArgumentException('Discount must be between 0 and 100');
		}
		if ($minDistance < 0 || $maxDistance < 0 || $minDistance > $maxDistance) {
			throw new \InvalidArgumentException('Invalid distance range');
		}
		if ($minWeight < 0 || $maxWeight < 0 || $minWeight > $maxWeight) {
			throw new \InvalidArgumentException('Invalid weight range');
		}

		$this->code = strtoupper($code);
	}

    public function getCode(): string
    {
        return $this->code;
    }

    public function isApplicable(Package $package): bool
    {
        $distance = $package->getDistance()->getKilometers();
        $weight = $package->getWeight()->getKilograms();

        return $distance >= $this->minDistance
            && $distance <= $this->maxDistance
            && $weight >= $this->minWeight
            && $weight <= $this->maxWeight;
    }

    public function calculateDiscount(Money $deliveryCost): Money
    {
        return $deliveryCost->percentage($this->discountPercentage);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'],
            $data['discount_percentage'],
            $data['min_distance'],
            $data['max_distance'],
            $data['min_weight'],
            $data['max_weight']
        );
    }
}