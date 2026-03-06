<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class ShortAnswerQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE_SHORT_ANSWER_QUESTIONS = 'getOnePageShortAnswerQuestions';
    public const SCENE_ADD_SHORT_ANSWER_QUESTIONS = 'addShortAnswerQuestions';
    public const SCENE_UPDATE_SHORT_ANSWER_QUESTIONS = 'updateShortAnswerQuestions';
    public const SCENE_DELETE_SHORT_ANSWER_QUESTIONS = 'deleteShortAnswerQuestions';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE_SHORT_ANSWER_QUESTIONS => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string|in:id,content,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,content',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD_SHORT_ANSWER_QUESTIONS => [
            'content' => 'required|string|max:500',
            'reference_answer' => 'required|string|max:1000',
        ],

        self::SCENE_UPDATE_SHORT_ANSWER_QUESTIONS => [
            'id' => 'required|integer:strict|gt:0',
            'content' => 'string|filled|max:500',
            'reference_answer' => 'string|filled|max:1000',
        ],

        self::SCENE_DELETE_SHORT_ANSWER_QUESTIONS => [
            'ids' => 'required|array',
            'ids.*' => 'integer:strict|gt:0',
        ]
    ];

    public function rules(): array
    {
        return [

        ];
    }

    public function attributes(): array
    {
        return [
            'page' => '页码',
            'orderBy' => '排序字段',
            'orderDirection' => '排序方向',
            'searchField' => '搜索字段',
            'searchValue' => '搜索值',
            'content' => '题目',
            'reference_answer' => '参考答案',
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [
            'content.filled' => ':attribute 不能为空',
        ];
    }
}
