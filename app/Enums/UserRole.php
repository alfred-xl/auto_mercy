<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case InventoryManager = 'inventory_manager';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::InventoryManager => 'Inventory Manager',
        };
    }
}
