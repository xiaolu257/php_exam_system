<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $role_id
 * @property int $permission_id
 * @property Carbon $created_at
 */
class RolePermission extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_permissions';

    public const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'permission_id' => 'integer', 'created_at' => 'datetime'];
}
