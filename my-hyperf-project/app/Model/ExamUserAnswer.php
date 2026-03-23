<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $exam_id
 * @property int $exam_paper_question_id
 * @property string $question_type
 * @property string $answer
 * @property int $is_correct
 * @property int $score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ExamUserAnswer extends Model
{

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam_user_answers';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [

    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'exam_id' => 'integer',
        'exam_paper_question_id' => 'integer',
        'answer' => 'array',
        'is_correct' => 'integer',
        'score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
