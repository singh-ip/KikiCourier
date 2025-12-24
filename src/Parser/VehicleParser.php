<?php

declare(strict_types=1);

namespace KikiCourier\Parser;

use KikiCourier\ValueObjects\Speed;
use KikiCourier\ValueObjects\Weight;

final class VehicleParser
{
    public function parseVehicleConfig(string $input): array
    {
        $parts = preg_split('/\s+/', trim($input));
        
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Invalid vehicle config format');
        }

        return [
            'count' => (int)$parts[0],
            'speed' => new Speed((float)$parts[1]),
            'max_weight' => new Weight((float)$parts[2])
        ];
    }
}