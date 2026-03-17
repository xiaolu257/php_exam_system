<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $paper_id
 * @property string $question_type
 * @property int $question_id
 * @property int $score
 * @property int $sort_order
 * @property string $question_snapshot
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class ExamPaperQuestion extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam_paper_questions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'paper_id' => 'integer',
        'question_id' => 'integer',
        'score' => 'integer',
        'sort_order' => 'integer',
        'question_snapshot' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'];
}
