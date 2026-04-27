<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Annotation\PublicAPI;
use App\Middleware\Helper\MiddlewareContext;
use App\Model\User;
use App\Model\UserToken;
use App\Util\TokenUtil;
use Firebase\JWT\ExpiredException;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 全局用户认证中间件
 */
class AuthMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected HttpResponse $response;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
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
        /** @var PublicAPI|null $publicAPI */
        $publicAPI = $annotations[PublicAPI::class] ?? null;
        if ($publicAPI) {
            MiddlewareContext::setSkipPermission();
            return $handler->handle($request);
        }

        try {
            // 获取前端传来的 Authorization 头部
            $authHeader = $request->getHeaderLine('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return $this->response->json(['msg' => '缺少或非法的 Authorization 头部'])->withStatus(422);
            }
            $postFingerprint = $request->getHeaderLine('Fingerprint');
            if (!$postFingerprint) {
                return $this->response->json(['msg' => '设备指纹不能为空'])->withStatus(422);
            }
            $accessToken = substr($authHeader, 7); // 去掉 Bearer 空格
            // 解码 Token（会校验签名和 exp）
            $decoded = TokenUtil::decodeToken($accessToken);
            $userId = $decoded->uid ?? null;
            $fingerprint = $decoded->fingerprint ?? null;

            if (!$userId || !$fingerprint) {
                return $this->response->json(['msg' => 'Token 数据不完整'])->withStatus(422);
            }

            if (!password_verify($postFingerprint, $fingerprint)) {
                return $this->response->json(['msg' => '设备指纹不一致'])->withStatus(401);
            }

            // 进一步验证数据库中的 token 是否存在
            $model = UserToken::where('user_id', $userId)
                ->select(['access_token'])
                ->first();
            if (!$model) {
                return $this->response->json(['msg' => '不存在相关用户'])->withStatus(404);
            }
            if (!password_verify($accessToken, $model->access_token)) {
                return $this->response->json(['msg' => '非法Token,code'])->withStatus(401);
            }
            // 可选：更新 token 的最后使用时间
            $model->save(['update_time' => date('Y-m-d H:i:s')]);

            // 加载用户数据
            $user = User::query()->select(['id'])->find($userId);
            if (!$user || !$user->id) {
                return $this->response->json(['success' => false, 'error' => '用户不存在']);
            }

            MiddlewareContext::setUserId($user->id);
            $permissions = User::leftJoin('user_roles as ur', 'users.id', '=', 'ur.user_id')
                ->leftJoin('roles as r', 'ur.role_id', '=', 'r.id')
                ->leftJoin('role_permissions as rp', 'r.id', '=', 'rp.role_id')
                ->leftJoin('permissions as p', 'rp.permission_id', '=', 'p.id')
                ->where('users.id', $userId)
                ->distinct()
                ->pluck('p.code')
                ->toArray();
            MiddlewareContext::setPermissions($permissions);
            return $handler->handle($request);
        } catch (ExpiredException $e) {
            return $this->response->json(['msg' => 'Token 已过期'])->withStatus(401);
        }
    }
}
