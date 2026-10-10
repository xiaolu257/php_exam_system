<?php

namespace App\Aspect;

use App\Annotation\AuthOnly;
use App\Middleware\Helper\MiddlewareContext;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\Redis;

#[Aspect]
class AuthOnlyAspect extends AbstractAspect
{
    #[Inject]
    protected Redis $redis;
    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    #[Inject]
    protected ResponseInterface $response;
    #[Inject]
    protected RequestInterface $request;
    public array $classes = [

    ];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public array $annotations = [
        AuthOnly::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        $userId = MiddlewareContext::getUserId();
        if (!$userId) {
            return $this->response->json(['msg' => '未登录用户无法使用本接口'])->withStatus(401);
        }

        $path = trim($this->request->getPathInfo(), '/');
        $method = $this->request->getMethod();

        // 1. 单用户接口级限流
        $ipKey = "$path:$method:userId:{$userId}";
        $ipCount = $this->redis->incr($ipKey);
        if ($ipCount === 1) $this->redis->expire($ipKey, 60);
        if ($ipCount > 30) {
            return $this->response->json(['msg' => '请求过于频繁，请稍后再试'])->withStatus(429);
        }
        // 在调用后进行某些处理
        return $proceedingJoinPoint->process();
    }
}
