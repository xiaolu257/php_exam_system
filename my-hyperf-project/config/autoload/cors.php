<?php

return [
    // 允许的来源（白名单）
    'allow_origins' => [
        'http://localhost:5173',
        'https://www.xxx.com',
    ],

    // 是否允许携带凭证（Cookie 场景才开）
    'allow_credentials' => false,

    // 允许的 Header
    'allow_headers' => [
        'DNT',
        'Keep-Alive',
        'User-Agent',
        'Cache-Control',
        'Content-Type',
        'Authorization',
        'Fingerprint',
    ],

    // 允许的方法
    'allow_methods' => [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH',
        'OPTIONS',
    ],
];
