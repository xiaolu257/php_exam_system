<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class SingleChoiceQuestionRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE_SINGLE_CHOICE_QUESTIONS = 'getOnePageSingleChoiceQuestions';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE_SINGLE_CHOICE_QUESTIONS => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:content,options,correct_answer',
            'searchValue' => 'required_with:searchField|string|filled|max:100'
        ],
    ];

    public function rules(): array
    {
        return [
            'username' => 'required|string|min:6|max:20',
            'password' => 'required|string|min:6|max:20',
            'nickname' => 'required|string|min:2|max:20',
            'avatar' => 'image|max:2048',
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
        ];
    }
}
