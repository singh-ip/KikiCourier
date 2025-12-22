<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Entity;

use KikiCourier\Entity\Vehicle;
use KikiCourier\ValueObjects\Weight;
use KikiCourier\ValueObjects\Speed;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class VehicleTest extends TestCase
{
    public function testCreatesVehicleWithProperties(): void
    {
        $vehicle = new Vehicle('V1', new Weight(200), new Speed(70));

        $this->assertSame('V1', $vehicle->getId());
        $this->assertSame(200.0, $vehicle->getMaxWeight()->getKilograms());
        $this->assertSame(70.0, $vehicle->getSpeed()->getKmPerHour());
        $this->assertSame(0.0, $vehicle->getAvailableAt());
    }

    public function testCannotCreateVehicleWithEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Vehicle ID cannot be empty');

        new Vehicle('', new Weight(200), new Speed(70));
    }

    public function testStartsAvailableAtTimeZero(): void
    {
        $vehicle = new Vehicle('V1', new Weight(200), new Speed(70));

        $this->assertSame(0.0, $vehicle->getAvailableAt());
    }

    public function testSetAvailableAtUpdatesAvailabilityTime(): void
    {
        $vehicle = new Vehicle('V1', new Weight(200), new Speed(70));

        $vehicle->setAvailableAt(3.5);

        $this->assertSame(3.5, $vehicle->getAvailableAt());
    }

    public function testSetAvailableAtRejectsNegativeTime(): void
    {
        $vehicle = new Vehicle('V1', new Weight(200), new Speed(70));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Time cannot be negative');

        $vehicle->setAvailableAt(-1.0);
    }
}
