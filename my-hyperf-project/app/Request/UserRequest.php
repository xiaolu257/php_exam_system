<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRequest extends FormRequest
{
    public const SCENE_LOGIN = 'login';
    public const SCENE_REGISTER = 'register';
    public const SCENE_GET_USER_AVATAR_THUMB = 'getUserAvatarThumb';
    public const SCENE_UPDATE_PROFILE = 'updateProfile';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_LOGIN => ['username', 'password'],
        self::SCENE_REGISTER => ['username', 'password', 'nickname', 'avatar'],
        self::SCENE_GET_USER_AVATAR_THUMB => ['avatarUrl' => 'required|string'],
        self::SCENE_UPDATE_PROFILE => [
            'username',
            'nickname' => 'required_without:avatar|filled|string|min:2|max:20',
            'avatar' => 'required_without:nickname|image|max:2048',
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
            'username' => '账号',
            'password' => '密码',
            'nickname' => '昵称',
            'avatar' => '头像',

        ];
    }
}
