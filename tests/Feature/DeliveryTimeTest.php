<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Feature;

use KikiCourier\Application\CourierServiceApp;
use PHPUnit\Framework\TestCase;

final class DeliveryTimeTest extends TestCase
{
    public function testEndToEndFlowMatchesSampleOutput(): void
    {
        $input = <<<'TXT'
        100 5
        PKG1 50 30 OFR001
        PKG2 75 125 OFR008
        PKG3 175 100 OFR003
        PKG4 110 60 OFR002
        PKG5 155 95 NA
        2 70 200
        TXT;

        $inputHandle = fopen('php://memory', 'r+');
        fwrite($inputHandle, $input . PHP_EOL);
        rewind($inputHandle);

        $outputHandle = fopen('php://memory', 'w+');

        $app = new CourierServiceApp($inputHandle, $outputHandle);

        $app->run();

        rewind($outputHandle);
        $output = stream_get_contents($outputHandle);

        $lines = array_values(
            array_filter(
                array_map('trim', explode(PHP_EOL, $output)),
                'strlen'
            )
        );

        $this->assertCount(5, $lines);

        $expected = [
            ['PKG1', '0', '750', 3.98],
            ['PKG2', '0', '1475', 1.78],
            ['PKG3', '0', '2350', 1.42],
            ['PKG4', '105', '1395', 0.85],
            ['PKG5', '0', '2125', 4.19],
        ];

        foreach ($expected as $index => [$id, $discount, $cost, $time]) {
            $parts = preg_split('/\s+/', $lines[$index]);
            $this->assertCount(4, $parts, 'Each output line must have 4 columns');

            [$actualId, $actualDiscount, $actualCost, $actualTime] = $parts;

            $this->assertSame($id, $actualId);
            $this->assertSame($discount, $actualDiscount);
            $this->assertSame($cost, $actualCost);

            $this->assertEqualsWithDelta(
                $time,
                (float) $actualTime,
                0.05,
                sprintf('Unexpected delivery time for %s', $id)
            );
        }
    }
}
