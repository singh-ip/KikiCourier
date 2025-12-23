<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Service;

use KikiCourier\Entity\Package;
use KikiCourier\Service\DeliveryCostCalculator;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class DeliveryCostCalculatorTest extends TestCase
{
    public function testCalculatesDeliveryCostCorrectly(): void
    {
        $calculator = new DeliveryCostCalculator(new Money(100));

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $cost = $calculator->calculateDeliveryCost($package);

        $this->assertSame(175.0, $cost->getAmount());
    }

    public function testCalculatesCostForPkg2Example(): void
    {
        $calculator = new DeliveryCostCalculator(new Money(100));

        $package = new Package(
            new PackageId('PKG2'),
            new Weight(15),
            new Distance(5)
        );

        $cost = $calculator->calculateDeliveryCost($package);

        $this->assertSame(275.0, $cost->getAmount());
    }

    public function testCalculatesCostForPkg3Example(): void
    {
        $calculator = new DeliveryCostCalculator(new Money(100));

        $package = new Package(
            new PackageId('PKG3'),
            new Weight(10),
            new Distance(100)
        );

        $cost = $calculator->calculateDeliveryCost($package);

        $this->assertSame(700.0, $cost->getAmount());
    }

    public function testCalculatesTotalCostWithDiscount(): void
    {
        $calculator = new DeliveryCostCalculator(new Money(100));

        $package = new Package(
            new PackageId('PKG3'),
            new Weight(10),
            new Distance(100)
        );

        $package->setDiscount(new Money(35));

        $deliveryCost = new Money(700);
        $totalCost = $calculator->calculateTotalCostWithDiscount($package, $deliveryCost);

        $this->assertSame(665.0, $totalCost->getAmount());
    }

    public function testCalculatesTotalCostWithZeroDiscount(): void
    {
        $calculator = new DeliveryCostCalculator(new Money(100));

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $deliveryCost = new Money(175);
        $totalCost = $calculator->calculateTotalCostWithDiscount($package, $deliveryCost);

        $this->assertSame(175.0, $totalCost->getAmount());
    }
}
