<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Unit\ValueObjects;

use KikiCourier\ValueObjects\PackageId;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class PackageIdTest extends TestCase
{
    public function testConstructsWithValidId(): void
    {
        $id = new PackageId('PKG1');
        
        $this->assertSame('PKG1', $id->getId());
    }

    public function testTrimsWhitespace(): void
    {
        $id = new PackageId('  PKG1  ');
        
        $this->assertSame('PKG1', $id->getId());
    }

    public function testRejectsEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Package ID cannot be empty');
        
        new PackageId('');
    }

    public function testRejectsWhitespaceOnlyId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Package ID cannot be empty');
        
        new PackageId('   ');
    }
}