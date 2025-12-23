<?php

declare(strict_types=1);

namespace KikiCourier\Service;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\Money;

final class OfferService
{
    public function __construct(private OfferRegistry $registry)
    {
    }

    public function applyOffer(Package $package, Money $deliveryCost): Money
    {
        if (!$package->hasOfferCode()) {
            return new Money(0);
        }

        $offer = $this->registry->findByCode($package->getOfferCode());
        
        if ($offer === null) {
            return new Money(0);
        }

        if (!$offer->isApplicable($package)) {
            return new Money(0);
        }

        return $offer->calculateDiscount($deliveryCost);
    }
}