<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Unit\ValueObjects;

use KikiCourier\ValueObjects\Weight;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class WeightTest extends TestCase
{
    public function testConstructsWithValidWeight(): void
    {
        $weight = new Weight(50.5);
        
        $this->assertSame(50.5, $weight->getKilograms());
    }

    public function testRejectsNegativeWeight(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Weight cannot be negative');
        
        new Weight(-5);
    }

    public function testAddsWeightsCorrectly(): void
    {
        $weight1 = new Weight(50);
        $weight2 = new Weight(30);
        
        $result = $weight1->add($weight2);
        
        $this->assertSame(80.0, $result->getKilograms());
    }

    public function testComparesWeightsCorrectly(): void
    {
        $lighter = new Weight(50);
        $heavier = new Weight(100);
        $equal = new Weight(50);
        
        $this->assertTrue($lighter->isLessThanOrEqual($heavier));
        $this->assertTrue($lighter->isLessThanOrEqual($equal));
        $this->assertFalse($heavier->isLessThanOrEqual($lighter));
    }

    public function testIsImmutable(): void
    {
        $original = new Weight(50);
        $modified = $original->add(new Weight(30));
        
        $this->assertSame(50.0, $original->getKilograms());
        $this->assertSame(80.0, $modified->getKilograms());
    }
}