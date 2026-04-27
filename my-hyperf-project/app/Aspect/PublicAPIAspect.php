<?php

namespace App\Aspect;

use App\Annotation\PublicAPI;
use App\Annotation\SomeAnnotation;
use App\Service\SomeClass;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\Redis;

#[Aspect]
class PublicAPIAspect extends AbstractAspect
{
    #[Inject]
    protected Redis $redis;
    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [

    ];

    // 要切入的注解，具体切入的还是使用了这些注解的类，仅可切入类注解和类方法注解
    public array $annotations = [
        PublicAPI::class,
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 切面切入后，执行对应的方法会由此来负责
        // $proceedingJoinPoint 为连接点，通过该类的 process() 方法调用原方法并获得结果
        // 在调用前进行某些处理
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        $response = ApplicationContext::getContainer()->get(ResponseInterface::class);
        $ip = $request->getServerParams()['remote_addr'];
        $fingerprint = $request->header('Fingerprint');
        $path = trim($request->getPathInfo(), '/');
        $method = $request->getMethod();

        // 1. IP限流（防御性）
        $ipKey = "$path:$method:ip:{$ip}";
        $ipCount = $this->redis->incr($ipKey);
        if ($ipCount === 1) $this->redis->expire($ipKey, 60);
        if ($ipCount > 100) {
            return $response->json(['msg' => '请求过于频繁，请稍后再试'])->withStatus(429);
        }

        // 2. 指纹限流（核心限流）
        $fingerprintKey = "$path:$method:fingerprint:{$fingerprint}";
        $fpCount = $this->redis->incr($fingerprintKey);
        if ($fpCount === 1) $this->redis->expire($fingerprintKey, 60);
        if ($fpCount > 10) {
            return $response->json(['msg' => '请求过于频繁，请稍后再试'])->withStatus(429);
        }
        
        // 在调用后进行某些处理
        return $proceedingJoinPoint->process();
    }
}
