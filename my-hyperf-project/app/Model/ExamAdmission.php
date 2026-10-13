<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $exam_paper_id
 * @property int $user_id
 * @property string $admission_method
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ExamAdmission extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'exam_admissions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id'            => 'integer',
        'exam_paper_id' => 'integer',
        'user_id'       => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
}
