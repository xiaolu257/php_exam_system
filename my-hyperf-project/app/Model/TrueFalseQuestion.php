<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 
 * @property string $content 
 * @property int $correct_answer 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class TrueFalseQuestion extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'true_false_questions';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'correct_answer' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
