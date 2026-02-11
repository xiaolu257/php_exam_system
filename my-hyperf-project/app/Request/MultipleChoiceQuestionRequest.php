<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class MultipleChoiceQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE_MULTIPLE_CHOICE_QUESTIONS = 'getOnePageMultipleChoiceQuestions';

    public const SCENE_ADD_MULTIPLE_CHOICE_QUESTIONS = 'addMultipleChoiceQuestions';
    public const SCENE_UPDATE_MULTIPLE_CHOICE_QUESTIONS = 'updateMultipleChoiceQuestions';
    public const SCENE_DELETE_MULTIPLE_CHOICE_QUESTIONS = 'deleteMultipleChoiceQuestions';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE_MULTIPLE_CHOICE_QUESTIONS => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string|in:id,content,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,content,options,correct_answer',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD_MULTIPLE_CHOICE_QUESTIONS => [
            'content' => 'required|string|max:255',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'string|filled|max:50',
            'correct_answer' => 'required|array|min:2|max:10',
            'correct_answer.*' => 'filled|regex:/^[A-J]$/',
        ],

        self::SCENE_UPDATE_MULTIPLE_CHOICE_QUESTIONS => [
            'id' => 'required|integer:strict|gt:0',
            'content' => 'string|max:255',
            'options' => 'array|min:2|max:10',
            'options.*' => 'string|filled|max:50',
            'correct_answer' => 'filled|regex:/^[A-J]$/',
        ],

        self::SCENE_DELETE_MULTIPLE_CHOICE_QUESTIONS => [
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
            'options' => '选项',
            'options.*' => '选项内容',
            'correct_answer' => '正确答案',
            'correct_answer.*' => '正确答案中的选项',
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [
            'options.*.filled' => ':attribute 不能为空',
            'correct_answer.*.regex' => ':attribute 必须是 A 到 J 之间的字母',
        ];
    }
}
