<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class MenuRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE = 'getOnePage';
    public const SCENE_ADD = 'add';
    public const SCENE_UPDATE = 'update';
    public const SCENE_DELETE = 'delete';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE => [
            'page' => 'required|integer|min:1',
            'orderBy' => 'string|in:id,parent_id,name,code,sort,created_at,updated_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,parent_id,name,code,sort',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:100',
            'parent_id' => 'required|integer:strict|min:0',
            'sort' => 'required|integer:strict|min:0',
        ],

        self::SCENE_UPDATE => [
            'id' => 'required|integer:strict|min:1',
            'name' => 'string|max:50',
            'code' => 'string|max:100',
            'parent_id' => 'integer:strict|min:0',
            'sort' => 'integer:strict|min:0',
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
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
