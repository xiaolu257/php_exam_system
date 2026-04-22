<?php

namespace App\Listener;

use App\Annotation\AuthOnly;
use App\Annotation\Permission;
use App\Annotation\PublicAPI;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

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

    private function collect(array &$map, array $methods, string $type): void
    {
        foreach ($methods as $item) {
            $key = $item['class'] . '::' . $item['method'];

            if (!isset($map[$key])) {
                $map[$key] = [];
            }

            $map[$key][] = $type;
        }
    }

    protected function checkConflict(): void
    {
        // 收集三类注解
        $publicMethods = AnnotationCollector::getMethodsByAnnotation(PublicAPI::class);
        $authOnlyMethods = AnnotationCollector::getMethodsByAnnotation(AuthOnly::class);
        $permissionMethods = AnnotationCollector::getMethodsByAnnotation(Permission::class);

        // ===== 校验 Permission name 唯一性 =====
        $permissionNameMap = [];

        foreach ($permissionMethods as $item) {
            /** @var Permission $annotation */
            $annotation = $item['annotation'];

            $name = $annotation->name;
            $location = $item['class'] . '::' . $item['method'];

            if (isset($permissionNameMap[$name])) {
                throw new \LogicException(sprintf(
                    '权限名重复，"%s"已存在于："%s"，重复定义于："%s"',
                    $name,
                    $permissionNameMap[$name],
                    $location
                ));
            }

            $permissionNameMap[$name] = $location;
        }

        // ===== 构建方法 → 注解列表 =====
        $methodMap = [];

        $this->collect($methodMap, $publicMethods, 'PublicAPI');
        $this->collect($methodMap, $authOnlyMethods, 'AuthOnly');
        $this->collect($methodMap, $permissionMethods, 'Permission');

        // ===== 检查冲突（关键）=====
        foreach ($methodMap as $method => $annotations) {
            if (count($annotations) > 1) {
                throw new \LogicException(sprintf(
                    '注解冲突：%s 同时使用了多个访问控制注解 [%s]',
                    $method,
                    implode(', ', $annotations)
                ));
            }
        }
    }
}