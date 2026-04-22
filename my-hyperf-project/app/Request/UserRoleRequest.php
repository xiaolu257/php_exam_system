<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRoleRequest extends FormRequest
{

    public const SCENE_GET_ONE_PAGE = 'getOnePage';
    public const SCENE_ADD = 'add';
    public const SCENE_DELETE = 'delete';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE => [
            'page' => 'required|integer|min:1',
            'orderBy' => 'string|in:id,user_id,username,nickname,role_id,role_code,role_description,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,user_id,username,nickname,role_id,role_code,role_description',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'user_id' => 'required|integer:strict|min:1',
            'role_id' => 'required|integer:strict|min:1',
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
            'role_id' => '关联角色',
            'permission_id' => '关联权限',
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }
}
