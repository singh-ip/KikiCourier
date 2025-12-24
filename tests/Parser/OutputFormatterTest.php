<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Parser;

use KikiCourier\Entity\Package;
use KikiCourier\Parser\OutputFormatter;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class OutputFormatterTest extends TestCase
{
    public function testFormatsPackageResultCorrectly(): void
    {
        $formatter = new OutputFormatter();

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $package->setDiscount(new Money(0));
        $package->setTotalCost(new Money(175));

        $output = $formatter->formatPackageResult($package);

        $this->assertSame('PKG1 0 175', $output);
    }

    public function testFormatsPackageWithDiscount(): void
    {
        $formatter = new OutputFormatter();

        $package = new Package(
            new PackageId('PKG3'),
            new Weight(10),
            new Distance(100)
        );

        $package->setDiscount(new Money(35));
        $package->setTotalCost(new Money(665));

        $output = $formatter->formatPackageResult($package);

        $this->assertSame('PKG3 35 665', $output);
    }
}
