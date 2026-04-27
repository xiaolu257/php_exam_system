<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Middleware\Helper\MiddlewareContext;
use Hyperf\RateLimit\Storage\RedisStorage;

/*return [
    'create' => 1,
    'consume' => 1,
    'capacity' => 2,
    'limitCallback' => [],
    'waitTimeout' => 1,
    'storage' => [
        'class' => RedisStorage::class,
        'options' => [
            'pool' => 'default',
            'expired_time' => 0,
        ],
    ],
];*/
return [
    'create' => 1,
    'consume' => 3,
    'capacity' => 6,
    'limitCallback' => [],
    'waitTimeout' => 2,
    'storage' => [
        'class' => RedisStorage::class,
        'options' => [
            'pool' => 'default',
            'expired_time' => 0,
        ],
    ],
    'key' => [MiddlewareContext::class, 'getUserId2'],
];
