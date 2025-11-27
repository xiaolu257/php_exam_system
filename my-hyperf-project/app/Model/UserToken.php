<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $access_token
 * @property string $refresh_token
 * @property string $ip_address
 * @property string $user_agent
 * @property string $expires_time
 * @property string $refresh_expires_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class UserToken extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_tokens';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['user_id', 'access_token', 'refresh_token', 'ip_address', 'user_agent', 'expires_time', 'refresh_expires_time'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * 自动加密 AccessToken
     *
     * @param string $value
     * @used-by
     */
    public function setAccessTokenAttribute(string $value): void
    {
        $this->attributes['access_token'] = password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 自动加密 RefreshToken
     * @param $value
     * @used-by
     */
    public function setRefreshTokenAttribute($value): void
    {
        $this->attributes['refresh_token'] = password_hash($value, PASSWORD_DEFAULT);
    }
}
