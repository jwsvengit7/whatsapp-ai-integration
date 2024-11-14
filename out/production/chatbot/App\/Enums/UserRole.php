<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case VENDOR = 'vendor';
    case USER = 'user';
    case SUPER_ADMIN = "super_admin";

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
