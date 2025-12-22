<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Util\CorsUtil;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 取出请求 Origin（可能为空）
        $origin = $request->getHeaderLine('Origin');

        // 预检请求：直接返回带 CORS 的空响应
        if ($request->getMethod() === 'OPTIONS') {
            $response = Context::get(ResponseInterface::class);
            return CorsUtil::apply($response, $origin);
        }

        // 正常请求
        $response = $handler->handle($request);

        return CorsUtil::apply($response, $origin);
    }
}
