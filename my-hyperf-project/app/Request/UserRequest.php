<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserRequest extends FormRequest
{
    public const SCENE_LOGIN = 'login';
    public const SCENE_REGISTER = 'register';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_LOGIN => ['username', 'password'],
        self::SCENE_REGISTER => ['username', 'password', 'nickname', 'avatar'],
    ];

    public function rules(): array
    {
        return [
            'username' => 'required|string|min:6|max:20',
            'password' => 'required|string|min:6|max:20',
            'nickname' => 'required|string|min:2|max:20',
            'avatar' => 'nullable|image|max:2048',
        ];
    }
}
