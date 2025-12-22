<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Unit\ValueObjects;

use KikiCourier\ValueObjects\Money;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class MoneyTest extends TestCase
{
    public function testConstructsWithValidAmount(): void
    {
        $money = new Money(100.50);
        
        $this->assertSame(100.50, $money->getAmount());
    }

    public function testRoundsToTwoDecimalPlaces(): void
    {
        $money = new Money(100.567);
        
        $this->assertSame(100.57, $money->getAmount());
    }

    public function testRejectsNegativeAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount cannot be negative');
        
        new Money(-10);
    }

    public function testAddsMoneyCorrectly(): void
    {
        $money1 = new Money(100);
        $money2 = new Money(50);
        
        $result = $money1->add($money2);
        
        $this->assertSame(150.0, $result->getAmount());
    }

    public function testSubtractsMoneyCorrectly(): void
    {
        $money1 = new Money(100);
        $money2 = new Money(30);
        
        $result = $money1->subtract($money2);
        
        $this->assertSame(70.0, $result->getAmount());
    }

    public function testCalculatesPercentageCorrectly(): void
    {
        $money = new Money(1000);
        
        $result = $money->percentage(10);
        
        $this->assertSame(100.0, $result->getAmount());
    }

    public function testIsImmutable(): void
    {
        $original = new Money(100);
        $modified = $original->add(new Money(50));
        
        $this->assertSame(100.0, $original->getAmount());
        $this->assertSame(150.0, $modified->getAmount());
        $this->assertNotSame($original, $modified);
    }
}