<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Service;

use KikiCourier\Entity\Package;
use KikiCourier\Offer\OfferFactory;
use KikiCourier\Service\OfferRegistry;
use KikiCourier\Service\OfferService;
use KikiCourier\ValueObjects\Distance;
use KikiCourier\ValueObjects\Money;
use KikiCourier\ValueObjects\PackageId;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class OfferServiceTest extends TestCase
{
    public function testAppliesValidOfferWhenCriteriaMet(): void
    {
        $registry = $this->createRegistryWithStandardOffers();
        $service = new OfferService($registry);

        $package = new Package(
            new PackageId('PKG3'),
            new Weight(10),
            new Distance(100),
            'OFR003'
        );

        $discount = $service->applyOffer($package, new Money(700));

        $this->assertSame(35.0, $discount->getAmount());
    }

    public function testReturnsZeroForInvalidOfferCode(): void
    {
        $registry = $this->createRegistryWithStandardOffers();
        $service = new OfferService($registry);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(10),
            new Distance(100),
            'INVALID'
        );

        $discount = $service->applyOffer($package, new Money(700));

        $this->assertSame(0.0, $discount->getAmount());
    }

    public function testReturnsZeroWhenCriteriaNotMet(): void
    {
        $registry = $this->createRegistryWithStandardOffers();
        $service = new OfferService($registry);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(5),
            new Distance(5),
            'OFR001'
        );

        $discount = $service->applyOffer($package, new Money(175));

        $this->assertSame(0.0, $discount->getAmount());
    }

    public function testReturnsZeroWhenNoOfferCode(): void
    {
        $registry = $this->createRegistryWithStandardOffers();
        $service = new OfferService($registry);

        $package = new Package(
            new PackageId('PKG1'),
            new Weight(10),
            new Distance(100),
            ''
        );

        $discount = $service->applyOffer($package, new Money(700));

        $this->assertSame(0.0, $discount->getAmount());
    }

    private function createRegistryWithStandardOffers(): OfferRegistry
    {
        $registry = new OfferRegistry();

        foreach (OfferFactory::createStandardOffers() as $offer) {
            $registry->register($offer);
        }

        return $registry;
    }
}
