<?php

namespace App\Aspect;

use App\Annotation\Permission;
use App\Middleware\Helper\MiddlewareContext;
use App\Model\User;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\Redis;

#[Aspect]
class PermissionAspect extends AbstractAspect
{
    #[Inject]
    protected Redis $redis;
    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [

    ];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public array $annotations = [
        Permission::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);
        $metadata = $proceedingJoinPoint->getAnnotationMetadata();
        /** @var Permission $permission */
        $permission = $metadata->method[Permission::class];
        $code = $permission->code;
        $userId = MiddlewareContext::getUserId();
        if (!$userId) {
            return $response->json(['msg' => '未登录用户无法使用本接口'])->withStatus(401);
        }
        $userPermission = User::leftJoin('user_roles as ur', 'users.id', '=', 'ur.user_id')
            ->leftJoin('roles as r', 'ur.role_id', '=', 'r.id')
            ->leftJoin('role_permissions as rp', 'r.id', '=', 'rp.role_id')
            ->leftJoin('permissions as p', 'rp.permission_id', '=', 'p.id')
            ->where('users.id', $userId)
            ->distinct()
            ->pluck('p.code')
            ->toArray();
        if (!in_array($code, $userPermission)) {
            return $response->json(['msg' => '您的权限不足，无法使用本接口'])->withStatus(403);
        }

        // 在调用后进行某些处理
        return $proceedingJoinPoint->process();
    }
}
