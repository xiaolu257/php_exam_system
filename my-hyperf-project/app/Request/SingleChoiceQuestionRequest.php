<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class SingleChoiceQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE = 'getOnePageSingleChoiceQuestions';
    public const SCENE_ADD = 'add';
    public const SCENE_UPDATE = 'update';
    public const SCENE_DELETE = 'delete';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string|in:id,content,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,content,options',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'content' => 'required|string|max:255',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'string|filled|max:50',
            'correct_answer' => 'required|regex:/^[A-J]$/',
        ],

        self::SCENE_UPDATE => [
            'id' => 'required|integer:strict|gt:0',
            'content' => 'string|filled|max:255',
            'options' => 'array|min:2|max:10',
            'options.*' => 'string|filled|max:50',
            'correct_answer' => 'string|filled|regex:/^[A-J]$/',
        ],

        self::SCENE_DELETE => [
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
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [
            'content.filled' => ':attribute 不能为空',
            'options.*.filled' => ':attribute 不能为空',
            'correct_answer.index_in' => ':attribute 必须存在于 :field 中',
            'correct_answer.regex' => ':attribute 必须是 A 到 J 之间的字母',
            'correct_answer.filled' => ':attribute 不能为空',
        ];
    }
}
