<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $action
 * @property string $permission_name
 * @property string $old_data
 * @property string $new_data
 * @property Carbon $created_at
 * @property string $batch_id
 */
class PermissionLog extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'permission_logs';

    public bool $timestamps = false;
    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'batch_id',
        'action',
        'permission_name',
        'old_data',
        'new_data',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime'
    ];
}
