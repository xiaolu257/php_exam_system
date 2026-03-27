<?php

namespace App\Middleware;

use App\Annotation\Permission;
use App\Middleware\Helper\MiddlewareContext;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected ResponseInterface $response;
    #[Inject]
    protected MiddlewareContext $authContext;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        /** @var Dispatched $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);
        [$class, $method] = $dispatched->handler->callback;
        // 获取注解
        $annotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        /** @var Permission|null $permission */
        $permission = $annotations[Permission::class] ?? null;
        // 没写权限注解 → 放行
        if (!$permission) {
            return $handler->handle($request);
        }
        $code = $permission->code;

        $userPermission = $this->authContext->getPermissions();
        if (!in_array($code, $userPermission)) {
            return $this->response->json(['msg' => '您的权限不足，无法使用本接口'])->withStatus(403);
        }
        return $handler->handle($request);
    }
}