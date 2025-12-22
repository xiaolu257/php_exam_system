<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Support\Cors;
use App\Util\CorsUtil;
use Hyperf\Context\Context;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(
        Throwable         $throwable,
        ResponseInterface $response
    ): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        /** @var ValidationException $throwable */
        $body = $throwable->validator->errors()->first();

        // 取出当前请求的 Origin（异常场景只能从 Context 取）
        $origin = Context::get(ServerRequestInterface::class)
            ?->getHeaderLine('Origin');

        $response = $response
            ->withStatus(422)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(json_encode(
                ['msg' => $body],
                JSON_UNESCAPED_UNICODE
            )));

        // 统一套 CORS
        return CorsUtil::apply($response, $origin);
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
