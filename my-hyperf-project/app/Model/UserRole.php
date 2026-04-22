<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $role_id
 * @property Carbon $created_at
 */
class UserRole extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_roles';

    public const UPDATED_AT = null;
    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'role_id' => 'integer', 'created_at' => 'datetime'];
}
