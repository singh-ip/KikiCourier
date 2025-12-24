<?php

declare(strict_types=1);

namespace KikiCourier\Parser;

use KikiCourier\Entity\Package;

final class DeliveryOutputFormatter
{
	public function formatPackageWithTime(Package $package): string
    {
        $id = $package->getId()->getId();
		$discount = number_format($package->getDiscount()->getAmount(), 0, '.', '');
		$totalCost = number_format($package->getTotalCost()->getAmount(), 0, '.', '');
		$deliveryTime = number_format($package->getEstimatedDeliveryTime(), 2, '.', '');

        return "{$id} {$discount} {$totalCost} {$deliveryTime}";
    }
}