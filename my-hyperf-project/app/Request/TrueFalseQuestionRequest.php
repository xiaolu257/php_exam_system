<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class TrueFalseQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE = 'getOnePage';
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
            'searchField' => 'string|in:id,content',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'content' => 'required|string|max:255',
            'correct_answer' => 'required|integer:strict|in:0,1',
        ],

        self::SCENE_UPDATE => [
            'id' => 'required|integer:strict|gt:0',
            'content' => 'string|filled|max:255',
            'correct_answer' => 'integer:strict|in:0,1',
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
            'correct_answer' => '正确答案',
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
