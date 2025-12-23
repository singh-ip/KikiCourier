<?php

declare(strict_types=1);

namespace KikiCourier\Offer;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\Money;

interface OfferInterface
{
    public function isApplicable(Package $package): bool;
    public function calculateDiscount(Money $deliveryCost): Money;
    public function getCode(): string;
}