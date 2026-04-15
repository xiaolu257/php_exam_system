<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $role_id
 * @property int $menu_id
 * @property Carbon $created_at
 */
class RoleMenu extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_menus';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'menu_id' => 'integer', 'created_at' => 'datetime'];
}
