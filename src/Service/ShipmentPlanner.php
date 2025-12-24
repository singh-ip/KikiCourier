<?php

declare(strict_types=1);

namespace KikiCourier\Service;

use KikiCourier\Entity\Shipment;
use KikiCourier\ValueObjects\Weight;

final class ShipmentPlanner
{
    public function __construct(private Weight $maxWeight)
    {
    }

    public function findBestShipment(array $packages): Shipment
    {
        if (empty($packages)) {
            return new Shipment();
        }

        $bestShipment = new Shipment();
        $this->findOptimalCombination($packages, new Shipment(), $bestShipment, 0);

        return $bestShipment;
    }

    private function findOptimalCombination(
        array $packages,
        Shipment $current,
        Shipment &$best,
        int $index
    ): void {
        if ($this->isBetter($current, $best)) {
            $best = clone $current;
        }

        for ($i = $index; $i < count($packages); $i++) {
            $package = $packages[$i];
            $newWeight = $current->getTotalWeight()->add($package->getWeight());

            if ($newWeight->isLessThanOrEqual($this->maxWeight)) {
                $current->addPackage($package);
                $this->findOptimalCombination($packages, $current, $best, $i + 1);

                $this->removeLastPackage($current);
            }
        }
    }

    private function isBetter(Shipment $candidate, Shipment $current): bool
    {
        $candidateCount = $candidate->getPackageCount();
        $currentCount = $current->getPackageCount();

        if ($candidateCount > $currentCount) {
            return true;
        }

        if ($candidateCount < $currentCount) {
            return false;
        }

        $candidateWeight = $candidate->getTotalWeight()->getKilograms();
        $currentWeight = $current->getTotalWeight()->getKilograms();

        if ($candidateWeight > $currentWeight) {
            return true;
        }

        if ($candidateWeight < $currentWeight) {
            return false;
        }

        $candidateDistance = $candidate->getMaxDistance();
        $currentDistance = $current->getMaxDistance();

        return $candidateDistance < $currentDistance;
    }

    private function removeLastPackage(Shipment $shipment): void
    {
        $shipment->removeLastPackage();
    }
}