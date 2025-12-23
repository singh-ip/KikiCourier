<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Offer;

use KikiCourier\Entity\Package;
use KikiCourier\Offer\Offer;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class OfferTest extends TestCase
{
    public function testIsApplicableReturnsTrueWhenWithinRanges(): void
    {
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(100),
            new Distance(100),
            'OFR001'
        );

        $this->assertTrue($offer->isApplicable($package));
    }

    public function testIsApplicableReturnsFalseWhenOutsideDistanceRange(): void
    {
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(100),
            new Distance(250),
            'OFR001'
        );

        $this->assertFalse($offer->isApplicable($package));
    }

    public function testIsApplicableReturnsFalseWhenOutsideWeightRange(): void
    {
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(50),
            new Distance(100),
            'OFR001'
        );

        $this->assertFalse($offer->isApplicable($package));
    }

    public function testCalculateDiscountReturnsExpectedPercentage(): void
    {
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $deliveryCost = new Money(700);
        $discount = $offer->calculateDiscount($deliveryCost);

        $this->assertSame(70.0, $discount->getAmount());
    }

    public function testFromArrayCreatesEquivalentOffer(): void
    {
        $data = [
            'code' => 'ofr001',
            'discount_percentage' => 10,
            'min_distance' => 0,
            'max_distance' => 200,
            'min_weight' => 70,
            'max_weight' => 200,
        ];

        $offer = Offer::fromArray($data);

        $this->assertSame('OFR001', $offer->getCode());

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(100),
            new Distance(100),
            'OFR001'
        );

        $this->assertTrue($offer->isApplicable($package));
    }
}
