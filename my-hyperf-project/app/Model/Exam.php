<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $exam_paper_id
 * @property Carbon $start_time
 * @property Carbon|null $submit_time
 * @property int $score
 * @property string $status
 * @property int $attempt_no
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class Exam extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exams';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer',
        'user_id' => 'integer',
        'exam_paper_id' => 'integer',
        'start_time' => 'datetime',
        'submit_time' => 'datetime',
        'score' => 'integer',
        'attempt_no' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'];
}
