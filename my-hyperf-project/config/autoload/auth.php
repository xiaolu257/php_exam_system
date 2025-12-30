<?php
/**
 * 自定义 权限校验配置
 */
return [
    //不需要权限校验的路由
    'except' => [
        'user/login',
        'user/register',
        'user/get-user-avatar-thumb',
        'user/get-user-avatar',
    ],
];
