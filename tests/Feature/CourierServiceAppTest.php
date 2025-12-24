<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Feature;

use KikiCourier\Application\CourierServiceApp;
use PHPUnit\Framework\TestCase;

final class CourierServiceAppTest extends TestCase
{
    public function testRunOutputsCostOnlyWhenNoVehicleConfigProvided(): void
    {
        $input = <<<'TXT'
        100 3
        PKG1 5 5 OFR001
        PKG2 15 5 OFR002
        PKG3 10 100 OFR003
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

        $this->assertSame([
            'PKG1 0 175',
            'PKG2 0 275',
            'PKG3 35 665',
        ], $lines);
    }
}

