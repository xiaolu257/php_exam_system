<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $content
 * @property string $options
 * @property string $correct_answer
 * @property int $score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SingleChoiceQuestion extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'single_choice_questions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'score' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
