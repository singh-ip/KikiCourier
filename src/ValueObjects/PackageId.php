<?php

declare(strict_types=1);

namespace KikiCourier\ValueObjects;

final class PackageId
{
    public function __construct(private string $id)
    {
        if (empty(trim($id))) {
            throw new \InvalidArgumentException('Package ID cannot be empty');
        }
        
        $this->id = trim($id);
    }

    public function getId(): string
    {
        return $this->id;
    }
}