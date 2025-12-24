<?php

declare(strict_types=1);

namespace KikiCourier\Entity;

use KikiCourier\ValueObjects\Weight;

final class Shipment
{
    private array $packages = [];
    private Weight $totalWeight;

    public function __construct()
    {
        $this->totalWeight = new Weight(0);
    }

    public function addPackage(Package $package): void
    {
        $this->packages[] = $package;
        $this->totalWeight = $this->totalWeight->add($package->getWeight());
    }

    public function removeLastPackage(): void
    {
        $removed = array_pop($this->packages);

        if ($removed === null) {
            return;
        }

        $total = 0.0;
        foreach ($this->packages as $package) {
            $total += $package->getWeight()->getKilograms();
        }

        $this->totalWeight = new Weight($total);
    }

    public function getPackages(): array
    {
        return $this->packages;
    }

    public function getTotalWeight(): Weight
    {
        return $this->totalWeight;
    }

    public function getPackageCount(): int
    {
        return count($this->packages);
    }

    public function isEmpty(): bool
    {
        return empty($this->packages);
    }

    public function getMaxDistance(): float
    {
        if (empty($this->packages)) {
            return 0.0;
        }

        $maxDistance = 0.0;
        foreach ($this->packages as $package) {
            $distance = $package->getDistance()->getKilometers();
            if ($distance > $maxDistance) {
                $maxDistance = $distance;
            }
        }
        return $maxDistance;
    }
}