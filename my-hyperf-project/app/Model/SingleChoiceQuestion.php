<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $content
 * @property array<string, string> $options
 * @property string $correct_answer
 * @property int $score
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SingleChoiceQuestion extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'single_choice_questions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['content', 'options', 'correct_answer'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'options' => 'json', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
