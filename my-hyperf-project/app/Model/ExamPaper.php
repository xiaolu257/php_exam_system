<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $duration
 * @property int $total_score
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int $max_attempts
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class ExamPaper extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam_papers';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'duration' => 'integer',
        'total_score' => 'integer',
        'max_attempts' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
