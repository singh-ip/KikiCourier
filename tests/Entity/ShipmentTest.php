<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Entity;

use KikiCourier\Entity\Shipment;
use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Distance;
use PHPUnit\Framework\TestCase;

final class ShipmentTest extends TestCase
{
    public function testNewShipmentIsEmptyWithZeroWeightAndZeroMaxDistance(): void
    {
        $shipment = new Shipment();

        $this->assertSame(0, $shipment->getPackageCount());
        $this->assertTrue($shipment->isEmpty());
        $this->assertSame(0.0, $shipment->getTotalWeight()->getKilograms());
        $this->assertSame(0.0, $shipment->getMaxDistance());
        $this->assertSame([], $shipment->getPackages());
    }

    public function testAddingPackagesAccumulatesWeightAndTracksMaxDistance(): void
    {
        $shipment = new Shipment();

        $package1 = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(10)
        );

        $package2 = new Package(
            new PackageId('PKG2'),
            new Weight(10),
            new Distance(25)
        );

        $package3 = new Package(
            new PackageId('PKG3'),
            new Weight(3),
            new Distance(7)
        );

        $shipment->addPackage($package1);
        $shipment->addPackage($package3);
        $shipment->addPackage($package2);

        $this->assertFalse($shipment->isEmpty());
        $this->assertSame(3, $shipment->getPackageCount());
        $this->assertSame(18.0, $shipment->getTotalWeight()->getKilograms());
        $this->assertSame(25.0, $shipment->getMaxDistance());

        $this->assertSame([
            $package1,
            $package3,
            $package2,
        ], $shipment->getPackages());
    }

    public function testRemoveLastPackageOnEmptyShipmentDoesNothing(): void
    {
        $shipment = new Shipment();

        $shipment->removeLastPackage();

        $this->assertSame(0, $shipment->getPackageCount());
        $this->assertTrue($shipment->isEmpty());
        $this->assertSame(0.0, $shipment->getTotalWeight()->getKilograms());
        $this->assertSame(0.0, $shipment->getMaxDistance());
        $this->assertSame([], $shipment->getPackages());
    }

    public function testRemoveLastPackageRemovesOnlyLastPackageAndUpdatesWeightAndMaxDistance(): void
    {
        $shipment = new Shipment();

        $package1 = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(10)
        );

        $package2 = new Package(
            new PackageId('PKG2'),
            new Weight(10),
            new Distance(25)
        );

        $package3 = new Package(
            new PackageId('PKG3'),
            new Weight(3),
            new Distance(7)
        );

        $shipment->addPackage($package1);
        $shipment->addPackage($package3);
        $shipment->addPackage($package2);

        $shipment->removeLastPackage();

        $this->assertFalse($shipment->isEmpty());
        $this->assertSame(2, $shipment->getPackageCount());
        $this->assertSame(8.0, $shipment->getTotalWeight()->getKilograms());
        $this->assertSame(10.0, $shipment->getMaxDistance());
        $this->assertSame(
            [
                $package1,
                $package3,
            ],
            $shipment->getPackages()
        );
    }

    public function testRemoveLastPackageRemovesSinglePackageAndResetsWeightAndMaxDistance(): void
    {
        $shipment = new Shipment();

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(10)
        );

        $shipment->addPackage($package);

        $shipment->removeLastPackage();

        $this->assertTrue($shipment->isEmpty());
        $this->assertSame(0, $shipment->getPackageCount());
        $this->assertSame(0.0, $shipment->getTotalWeight()->getKilograms());
        $this->assertSame(0.0, $shipment->getMaxDistance());
        $this->assertSame([], $shipment->getPackages());
    }
}
