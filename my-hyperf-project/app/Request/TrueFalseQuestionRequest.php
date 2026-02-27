<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class TrueFalseQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE_TRUE_FALSE_QUESTIONS = 'getOnePageTrueFalseQuestions';
    public const SCENE_ADD_TRUE_FALSE_QUESTIONS = 'addTrueFalseQuestions';
    public const SCENE_UPDATE_TRUE_FALSE_QUESTIONS = 'updateTrueFalseQuestions';
    public const SCENE_DELETE_TRUE_FALSE_QUESTIONS = 'deleteTrueFalseQuestions';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE_TRUE_FALSE_QUESTIONS => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string|in:id,content,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,content',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD_TRUE_FALSE_QUESTIONS => [
            'content' => 'required|string|max:255',
            'correct_answer' => 'required|integer|in:0,1',
        ],

        self::SCENE_UPDATE_TRUE_FALSE_QUESTIONS => [
            'id' => 'required|integer:strict|gt:0',
            'content' => 'string|max:255',
            'correct_answer' => 'integer|in:0,1',
        ],

        self::SCENE_DELETE_TRUE_FALSE_QUESTIONS => [
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
            'correct_answer' => '正确答案',
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [
            'correct_answer.index_in' => ':attribute 必须存在于 :field 中',
            'correct_answer.regex' => ':attribute 必须是 A 到 J 之间的字母',
            'correct_answer.filled' => ':attribute 不能为空',
        ];
    }
}
