<?php

declare(strict_types=1);

namespace KikiCourier\Offer;

final class OfferFactory
{
    public static function createStandardOffers(): array
    {
        return [
            new Offer('OFR001', 10, 0, 200, 70, 200),
            new Offer('OFR002', 7, 50, 150, 100, 250),
            new Offer('OFR003', 5, 50, 250, 10, 150),
        ];
    }

    public static function loadFromJson(string $jsonFile): array
    {
        if (!file_exists($jsonFile)) {
            throw new \RuntimeException("Offer config file not found: {$jsonFile}");
        }
        
        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON: " . json_last_error_msg());
        }
        
        $offers = [];
        foreach ($data['offers'] as $offerData) {
            $offers[] = Offer::fromArray($offerData);
        }
        
        return $offers;
    }
}