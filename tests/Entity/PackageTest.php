<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Entity;

use KikiCourier\Entity\Package;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use InvalidArgumentException;
use RuntimeException;
use PHPUnit\Framework\TestCase;

final class PackageTest extends TestCase
{
    public function testCreatesPackageWithAllProperties(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5),
            'OFR001'
        );

        $this->assertSame('PKG1', $package->getId()->getId());
        $this->assertSame(5.0, $package->getWeight()->getKilograms());
        $this->assertSame(5.0, $package->getDistance()->getKilometers());
        $this->assertSame('OFR001', $package->getOfferCode());
    }

    public function testNormalizesOfferCodeToUppercase(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5),
            'ofr001'
        );

        $this->assertSame('OFR001', $package->getOfferCode());
    }

    public function testDetectsWhenPackageHasOfferCode(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5),
            'OFR001'
        );

        $this->assertTrue($package->hasOfferCode());
    }

    public function testDetectsWhenPackageHasNoOfferCode(): void
    {
        $package1 = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5),
            ''
        );

        $package2 = new Package(
            new PackageId('PKG2'),
            new Weight(5),
            new Distance(5),
            'NA'
        );

        $this->assertFalse($package1->hasOfferCode());
        $this->assertFalse($package2->hasOfferCode());
    }

    public function testCanSetAndGetDiscount(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $package->setDiscount(new Money(35));

        $this->assertSame(35.0, $package->getDiscount()->getAmount());
    }

    public function testReturnsZeroDiscountWhenNotSet(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $this->assertSame(0.0, $package->getDiscount()->getAmount());
    }

    public function testCanSetAndGetTotalCost(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $package->setTotalCost(new Money(100));

        $this->assertSame(100.0, $package->getTotalCost()->getAmount());
    }

    public function testGetTotalCostThrowsWhenNotSet(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Total cost not calculated yet');

        $package->getTotalCost();
    }

    public function testCanSetAndGetEstimatedDeliveryTime(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $package->setEstimatedDeliveryTime(5.5);

        $this->assertSame(5.5, $package->getEstimatedDeliveryTime());
        $this->assertTrue($package->hasEstimatedDeliveryTime());
    }

    public function testSetEstimatedDeliveryTimeRejectsNegativeValue(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Delivery time cannot be negative');

        $package->setEstimatedDeliveryTime(-1);
    }

    public function testGetEstimatedDeliveryTimeThrowsWhenNotSet(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Delivery time not calculated yet');

        $package->getEstimatedDeliveryTime();
    }

    public function testHasEstimatedDeliveryTimeIsFalseWhenNotSet(): void
    {
        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5)
        );

        $this->assertFalse($package->hasEstimatedDeliveryTime());
    }
}