<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Parser;

use KikiCourier\Parser\VehicleParser;
use KikiCourier\ValueObjects\Speed;
use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;

final class VehicleParserTest extends TestCase
{
    public function testParsesVehicleConfigCorrectly(): void
    {
        $parser = new VehicleParser();

        $result = $parser->parseVehicleConfig('2 70 200');

        $this->assertSame(2, $result['count']);

        $this->assertInstanceOf(Speed::class, $result['speed']);
        $this->assertSame(70.0, $result['speed']->getKmPerHour());

        $this->assertInstanceOf(Weight::class, $result['max_weight']);
        $this->assertSame(200.0, $result['max_weight']->getKilograms());
    }

    public function testThrowsOnInvalidVehicleConfigFormat(): void
    {
        $parser = new VehicleParser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid vehicle config format');

        $parser->parseVehicleConfig('2 70');
    }
}

