<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Parser;

use KikiCourier\Entity\Package;
use KikiCourier\Parser\DeliveryOutputFormatter;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class DeliveryOutputFormatterTest extends TestCase
{
    public function testFormatsPackageWithTimeCorrectly(): void
    {
        $formatter = new DeliveryOutputFormatter();

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $package->setDiscount(new Money(0));
        $package->setTotalCost(new Money(175));
        $package->setEstimatedDeliveryTime(0.42);

        $output = $formatter->formatPackageWithTime($package);

        $this->assertSame('PKG1 0 175 0.42', $output);
    }

    public function testFormatsPackageWithTimeRoundsToTwoDecimals(): void
    {
        $formatter = new DeliveryOutputFormatter();

        $package = new Package(
            new PackageId('PKG2'),
            new Weight(10),
            new Distance(100)
        );

        $package->setDiscount(new Money(35));
        $package->setTotalCost(new Money(665));
        $package->setEstimatedDeliveryTime(1.2);

        $output = $formatter->formatPackageWithTime($package);

        $this->assertSame('PKG2 35 665 1.20', $output);
    }
}

