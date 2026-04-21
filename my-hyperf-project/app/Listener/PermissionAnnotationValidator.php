<?php

namespace App\Listener;

use App\Annotation\Permission;
use App\Annotation\PublicAPI;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use LogicException;

#[Listener]
class PermissionAnnotationValidator implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        $this->checkConflict();
    }

    protected function checkConflict(): void
    {
        //获取注解元信息
        $permissionMethods = AnnotationCollector::getMethodsByAnnotation(Permission::class);
        $publicAPIMethods = AnnotationCollector::getMethodsByAnnotation(PublicAPI::class);
        if (empty($permissionMethods) || empty($publicAPIMethods)) {
            return;
        }
        // 构建 PublicAPI 索引
        $publicAPIIndex = [];
        foreach ($publicAPIMethods as $item) {
            $key = $item['class'] . '::' . $item['method'];
            $publicAPIIndex[$key] = true;
        }
        // 检查冲突
        foreach ($permissionMethods as $item) {
            $key = $item['class'] . '::' . $item['method'];
            if (isset($publicAPIIndex[$key])) {
                throw new LogicException(
                    sprintf('注解冲突：%s::%s 同时使用了 RBAC 和 PublicAPI', $item['class'], $item['method'])
                );
            }
        }
    }
}