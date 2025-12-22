<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Unit\ValueObjects;

use KikiCourier\ValueObjects\Distance;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class DistanceTest extends TestCase
{
    public function testConstructsWithValidDistance(): void
    {
        $distance = new Distance(100);
        
        $this->assertSame(100.0, $distance->getKilometers());
    }

    public function testRejectsNegativeDistance(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Distance cannot be negative');
        
        new Distance(-10);
    }

    public function testHandlesZeroDistance(): void
    {
        $distance = new Distance(0);
        
        $this->assertSame(0.0, $distance->getKilometers());
    }
}