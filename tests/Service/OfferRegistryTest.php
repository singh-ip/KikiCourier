<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Service;

use KikiCourier\Offer\Offer;
use KikiCourier\Service\OfferRegistry;
use PHPUnit\Framework\TestCase;

final class OfferRegistryTest extends TestCase
{
    public function testRegisterAndFindOfferByCode(): void
    {
        $registry = new OfferRegistry();
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $registry->register($offer);
        $found = $registry->findByCode('OFR001');

        $this->assertNotNull($found);
        $this->assertSame('OFR001', $found->getCode());
        $this->assertSame($offer, $found);
    }

    public function testFindOfferCaseInsensitively(): void
    {
        $registry = new OfferRegistry();
        $offer = new Offer('OFR001', 10, 0, 200, 70, 200);

        $registry->register($offer);

        $found = $registry->findByCode('ofr001');

        $this->assertNotNull($found);
        $this->assertSame('OFR001', $found->getCode());
        $this->assertSame($offer, $found);
    }

    public function testReturnsNullForUnknownCode(): void
    {
        $registry = new OfferRegistry();

        $found = $registry->findByCode('UNKNOWN');

        $this->assertNull($found);
    }
}
