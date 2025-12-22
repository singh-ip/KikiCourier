<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Unit\ValueObjects;

use KikiCourier\ValueObjects\Speed;
use KikiCourier\ValueObjects\Distance;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class SpeedTest extends TestCase
{
    public function testConstructsWithValidSpeed(): void
    {
        $speed = new Speed(70);
        
        $this->assertSame(70.0, $speed->getKmPerHour());
    }

    public function testRejectsZeroSpeed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Speed must be positive');
        
        new Speed(0);
    }

    public function testRejectsNegativeSpeed(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Speed must be positive');
        
        new Speed(-10);
    }

    public function testCalculatesTimeCorrectly(): void
    {
        $speed = new Speed(70);
        $distance = new Distance(140);
        
        $time = $speed->calculateTime($distance);
        
        $this->assertSame(2.0, $time);
    }

    public function testCalculatesTimeWithDecimal(): void
    {
        $speed = new Speed(70);
        $distance = new Distance(30);
        
        $time = $speed->calculateTime($distance);
        
        $this->assertEqualsWithDelta(0.428571, $time, 0.000001);
    }
}