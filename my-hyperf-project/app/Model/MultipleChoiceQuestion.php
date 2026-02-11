<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $content
 * @property array<string,string> $options
 * @property array $correct_answer
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 */
class MultipleChoiceQuestion extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'multiple_choice_questions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'options' => 'array', 'correct_answer' => 'array', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
