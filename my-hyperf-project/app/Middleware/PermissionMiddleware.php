<?php

namespace App\Middleware;

use App\Annotation\AuthOnly;
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

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // Auth中间件若加入跳过权限校验标志，直接放行
        if (MiddlewareContext::getSkipPermission()) {
            return $handler->handle($request);
        }
        /** @var Dispatched|null $dispatched */
        $dispatched = $request->getAttribute(Dispatched::class);
        // 路由没匹配统一404处理
        if (!$dispatched || !$dispatched->handler || !isset($dispatched->handler->callback)) {
            return $this->response->json(['msg' => '访问的接口不存在'])->withStatus(404);
        }
        [$class, $method] = $dispatched->handler->callback;
        // 获取注解
        $annotations = AnnotationCollector::getClassMethodAnnotation($class, $method);
        //检查是否有公开接口注解,若有直接放行
        /** @var AuthOnly|null $authOnly */
        $authOnly = $annotations[AuthOnly::class] ?? null;
        if ($authOnly) {
            return $handler->handle($request);
        }
        /** @var Permission|null $permission */
        $permission = $annotations[Permission::class] ?? null;
        // 没写权限注解（且没有AuthOnly注解） → 直接拦截
        if (!$permission) {
            return $this->response->json(['msg' => '您的权限不足，无法使用本接口'])->withStatus(403);
        }
        $name = $permission->name;

        $userPermission = MiddlewareContext::getPermissions();
        if (!in_array($name, $userPermission)) {
            return $this->response->json(['msg' => '您的权限不足，无法使用本接口'])->withStatus(403);
        }
        return $handler->handle($request);
    }
}