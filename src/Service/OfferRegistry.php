<?php

declare(strict_types=1);

namespace KikiCourier\Service;

use KikiCourier\Offer\OfferInterface;

final class OfferRegistry
{
    private array $offers = [];

    public function register(OfferInterface $offer): void
    {
        $this->offers[$offer->getCode()] = $offer;
    }

    public function findByCode(string $code): ?OfferInterface
    {
        $normalizedCode = strtoupper(trim($code));
        return $this->offers[$normalizedCode] ?? null;
    }
}