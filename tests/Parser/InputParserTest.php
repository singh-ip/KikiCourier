<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Parser;

use KikiCourier\Parser\InputParser;
use PHPUnit\Framework\TestCase;

final class InputParserTest extends TestCase
{
    public function testParsesBaseCostAndPackageCount(): void
    {
        $parser = new InputParser();

        $result = $parser->parseBaseAndCount('100 3');

        $this->assertSame(100.0, $result['base_cost']->getAmount());
        $this->assertSame(3, $result['package_count']);
    }

    public function testParsesPackageWithOfferCode(): void
    {
        $parser = new InputParser();

        $package = $parser->parsePackage('PKG1 5 5 OFR001');

        $this->assertSame('PKG1', $package->getId()->getId());
        $this->assertSame(5.0, $package->getWeight()->getKilograms());
        $this->assertSame(5.0, $package->getDistance()->getKilometers());
        $this->assertSame('OFR001', $package->getOfferCode());
    }

    public function testParsesPackageWithoutOfferCode(): void
    {
        $parser = new InputParser();

        $package = $parser->parsePackage('PKG1 5 5');

        $this->assertSame('PKG1', $package->getId()->getId());
        $this->assertSame('', $package->getOfferCode());
    }
}
