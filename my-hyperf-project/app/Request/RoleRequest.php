<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class RoleRequest extends FormRequest
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
            'orderBy' => 'string|in:id,code,created_at,updated_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,code,description',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'code' => 'required|string|max:100',
            'description' => 'required|string|max:255',
        ],

        self::SCENE_UPDATE => [
            'id' => 'required|integer:strict|min:1',
            'code' => 'string|filled|max:100',
            'description' => 'string|filled|max:255',
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
            'ids.*' => 'ID列表的每一项ID',
            'code' => '角色标识',
            'description' => '描述',
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
