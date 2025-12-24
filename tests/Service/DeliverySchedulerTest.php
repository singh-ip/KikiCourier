<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Service;

use KikiCourier\Entity\Package;
use KikiCourier\Entity\Vehicle;
use KikiCourier\Service\DeliveryScheduler;
use KikiCourier\Service\ShipmentPlanner;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Speed;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class DeliverySchedulerTest extends TestCase
{
    public function testSchedulesDeliveryForSinglePackage(): void
    {
        $vehicles = [
            new Vehicle('V1', new Weight(200), new Speed(70)),
        ];

        $planner = new ShipmentPlanner(new Weight(200));
        $scheduler = new DeliveryScheduler($vehicles, $planner);

        $packages = [
            new Package(new PackageId('PKG1'), new Weight(50), new Distance(30)),
        ];

        $scheduler->scheduleDeliveries($packages);

        $this->assertTrue($packages[0]->hasEstimatedDeliveryTime());
        $this->assertEqualsWithDelta(0.42, $packages[0]->getEstimatedDeliveryTime(), 0.01);
    }

    public function testUsesVehicleWithEarliestAvailability(): void
    {
        $v1 = new Vehicle('V1', new Weight(200), new Speed(70));
        $v2 = new Vehicle('V2', new Weight(200), new Speed(70));

        $v1->setAvailableAt(5.0);
        $v2->setAvailableAt(3.0);

        $vehicles = [$v1, $v2];

        $planner = new ShipmentPlanner(new Weight(200));
        $scheduler = new DeliveryScheduler($vehicles, $planner);

        $packages = [
            new Package(new PackageId('PKG1'), new Weight(50), new Distance(30)),
        ];

        $scheduler->scheduleDeliveries($packages);

        $this->assertEqualsWithDelta(3.42, $packages[0]->getEstimatedDeliveryTime(), 0.01);
    }

    public function testSchedulesDeliveriesMatchingSampleOutput(): void
    {
        $vehicles = [
            new Vehicle('V1', new Weight(200), new Speed(70)),
            new Vehicle('V2', new Weight(200), new Speed(70)),
        ];

        $planner = new ShipmentPlanner(new Weight(200));
        $scheduler = new DeliveryScheduler($vehicles, $planner);

        $packages = [
            new Package(new PackageId('PKG1'), new Weight(50), new Distance(30)),
            new Package(new PackageId('PKG2'), new Weight(75), new Distance(125)),
            new Package(new PackageId('PKG3'), new Weight(175), new Distance(100)),
            new Package(new PackageId('PKG4'), new Weight(110), new Distance(60)),
            new Package(new PackageId('PKG5'), new Weight(155), new Distance(95)),
        ];

        $scheduler->scheduleDeliveries($packages);

        $this->assertEqualsWithDelta(3.98, $packages[0]->getEstimatedDeliveryTime(), 0.1);
        $this->assertEqualsWithDelta(1.78, $packages[1]->getEstimatedDeliveryTime(), 0.1);
        $this->assertEqualsWithDelta(1.42, $packages[2]->getEstimatedDeliveryTime(), 0.1);
        $this->assertEqualsWithDelta(0.85, $packages[3]->getEstimatedDeliveryTime(), 0.1);
        $this->assertEqualsWithDelta(4.19, $packages[4]->getEstimatedDeliveryTime(), 0.1);
    }
}
