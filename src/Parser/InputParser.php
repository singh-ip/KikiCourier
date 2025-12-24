<?php

declare(strict_types=1);

namespace KikiCourier\Parser;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;

final class InputParser
{
    public function parseBaseAndCount(string $input): array
    {
        $parts = preg_split('/\s+/', trim($input));
        
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid input format');
        }

        return [
            'base_cost' => new Money((float)$parts[0]),
            'package_count' => (int)$parts[1]
        ];
    }

    public function parsePackage(string $input): Package
    {
        $parts = preg_split('/\s+/', trim($input));
        
        if (count($parts) < 3) {
            throw new \InvalidArgumentException('Invalid package format');
        }

        $id = new PackageId($parts[0]);
        $weight = new Weight((float)$parts[1]);
        $distance = new Distance((float)$parts[2]);
        $offerCode = $parts[3] ?? '';

        return new Package($id, $weight, $distance, $offerCode);
    }
}