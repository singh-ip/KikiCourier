<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Service;

use KikiCourier\Entity\Package;
use KikiCourier\Service\ShipmentPlanner;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class ShipmentPlannerTest extends TestCase
{
    public function testReturnsEmptyShipmentForNoPackages(): void
    {
        $planner = new ShipmentPlanner(new Weight(200));

        $shipment = $planner->findBestShipment([]);

        $this->assertTrue($shipment->isEmpty());
    }

    public function testSelectsSinglePackageThatFits(): void
    {
        $planner = new ShipmentPlanner(new Weight(200));

        $packages = [
            new Package(new PackageId('PKG1'), new Weight(50), new Distance(30)),
        ];

        $shipment = $planner->findBestShipment($packages);

        $this->assertSame(1, $shipment->getPackageCount());
        $this->assertSame(50.0, $shipment->getTotalWeight()->getKilograms());
    }

    public function testPrefersMorePackagesOverSingleHeavierPackage(): void
    {
        $planner = new ShipmentPlanner(new Weight(200));

        $packages = [
            new Package(new PackageId('PKG1'), new Weight(50), new Distance(30)),
            new Package(new PackageId('PKG2'), new Weight(75), new Distance(125)),
            new Package(new PackageId('PKG3'), new Weight(175), new Distance(100)),
        ];

        $shipment = $planner->findBestShipment($packages);

        $this->assertSame(2, $shipment->getPackageCount());
    }
}
