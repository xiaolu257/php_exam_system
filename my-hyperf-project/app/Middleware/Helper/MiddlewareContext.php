<?php

namespace App\Middleware\Helper;


use Hyperf\Context\Context;

class MiddlewareContext
{
    private const USER_ID = 'auth.user_id';
    private const PERMISSIONS = 'auth.permissions';
    public const SKIP_PERMISSION = 'skip_permission';
    private const ROLES = 'auth.roles';

    public static function setUserId(int $userId): void
    {
        Context::set(self::USER_ID, $userId);
    }

    public static function getUserId(): ?int
    {
        return Context::get(self::USER_ID);
    }

    public static function setPermissions(array $permissions): void
    {
        Context::set(self::PERMISSIONS, $permissions);
    }

    public static function getPermissions(): array
    {
        return Context::get(self::PERMISSIONS, []);
    }

    public static function setSkipPermission(): void
    {
        Context::set(self::SKIP_PERMISSION, true);
    }

    public static function getSkipPermission(): ?bool
    {
        return Context::get(self::SKIP_PERMISSION);
    }
}