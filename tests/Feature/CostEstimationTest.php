<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Feature;

use KikiCourier\Offer\OfferFactory;
use KikiCourier\Parser\InputParser;
use KikiCourier\Parser\OutputFormatter;
use KikiCourier\Service\DeliveryCostCalculator;
use KikiCourier\Service\OfferRegistry;
use KikiCourier\Service\OfferService;
use KikiCourier\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class CostEstimationTest extends TestCase
{
    public function testMatchesExpectedOutputForSampleInput(): void
    {
        $parser = new InputParser();
        $formatter = new OutputFormatter();

        $registry = new OfferRegistry();
        foreach (OfferFactory::createStandardOffers() as $offer) {
            $registry->register($offer);
        }

        $calculator = new DeliveryCostCalculator(new Money(100));
        $offerService = new OfferService($registry);

        $pkg1 = $parser->parsePackage('PKG1 5 5 OFR001');
        $cost1 = $calculator->calculateDeliveryCost($pkg1);
        $discount1 = $offerService->applyOffer($pkg1, $cost1);
        $pkg1->setDiscount($discount1);
        $pkg1->setTotalCost($calculator->calculateTotalCostWithDiscount($pkg1, $cost1));

        $this->assertSame('PKG1 0 175', $formatter->formatPackageResult($pkg1));

        $pkg2 = $parser->parsePackage('PKG2 15 5 OFR002');
        $cost2 = $calculator->calculateDeliveryCost($pkg2);
        $discount2 = $offerService->applyOffer($pkg2, $cost2);
        $pkg2->setDiscount($discount2);
        $pkg2->setTotalCost($calculator->calculateTotalCostWithDiscount($pkg2, $cost2));

        $this->assertSame('PKG2 0 275', $formatter->formatPackageResult($pkg2));

        $pkg3 = $parser->parsePackage('PKG3 10 100 OFR003');
        $cost3 = $calculator->calculateDeliveryCost($pkg3);
        $discount3 = $offerService->applyOffer($pkg3, $cost3);
        $pkg3->setDiscount($discount3);
        $pkg3->setTotalCost($calculator->calculateTotalCostWithDiscount($pkg3, $cost3));

        $this->assertSame('PKG3 35 665', $formatter->formatPackageResult($pkg3));
    }
}
