<?php

declare(strict_types=1);

namespace KikiCourier\Service;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\Money;

final class DeliveryCostCalculator
{
    private const WEIGHT_MULTIPLIER = 10;
    private const DISTANCE_MULTIPLIER = 5;

	public function __construct(private Money $baseDeliveryCost)
	{
	}

    public function calculateDeliveryCost(Package $package): Money
    {
        $weightCost = $package->getWeight()->getKilograms() * self::WEIGHT_MULTIPLIER;
        $distanceCost = $package->getDistance()->getKilometers() * self::DISTANCE_MULTIPLIER;
        
        $totalCost = $this->baseDeliveryCost->getAmount() + $weightCost + $distanceCost;
        
        return new Money($totalCost);
    }

    public function calculateTotalCostWithDiscount(Package $package, Money $deliveryCost): Money
    {
        $discount = $package->getDiscount();
        return $deliveryCost->subtract($discount);
    }
}