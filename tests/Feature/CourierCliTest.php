<?php

declare(strict_types=1);

namespace KikiCourier\Tests\Feature;

use PHPUnit\Framework\TestCase;

final class CourierCliTest extends TestCase
{
    private function runCourier(string $input, ?int &$exitCode, ?string &$stdout, ?string &$stderr): void
    {
        $phpBinary = PHP_BINARY;
        $script = __DIR__ . '/../../bin/courier';
        $command = escapeshellarg($phpBinary) . ' ' . escapeshellarg($script);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, __DIR__ . '/../../');

        $this->assertIsResource($process, 'Failed to start courier CLI process');

        fwrite($pipes[0], $input);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
    }

    public function testNormalExecutionOutputsExpectedDeliveryTimes(): void
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

        $this->runCourier($input . PHP_EOL, $exitCode, $stdout, $stderr);

        $this->assertSame(0, $exitCode, 'Expected successful exit code');
        $this->assertSame('', trim($stderr), 'Expected no error output');

        $lines = array_values(
            array_filter(
                array_map('trim', explode(PHP_EOL, $stdout)),
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

    public function testErrorPathOnInvalidInput(): void
    {
        $input = "invalid\n";

        $this->runCourier($input, $exitCode, $stdout, $stderr);

        $this->assertNotSame(0, $exitCode, 'Expected non-zero exit code on error');
        $this->assertSame('', trim($stdout), 'Expected no normal output on error');
        $this->assertStringStartsWith('Error: Invalid input format', trim($stderr));
    }
}

