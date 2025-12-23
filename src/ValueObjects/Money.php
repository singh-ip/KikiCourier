<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class Money
{
    public function __construct(private float $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
        
        $this->amount = round($amount, 2);
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function add(Money $money): Money
    {
        return new Money($this->amount + $money->getAmount());
    }

    public function subtract(Money $money): Money
    {
        return new Money($this->amount - $money->getAmount());
    }

    public function percentage(float $percent): Money
    {
        return new Money($this->amount * ($percent / 100));
    }
}
