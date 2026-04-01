<?php

namespace App\Command;

use App\Annotation\Permission;
use App\Model\Permission as PermissionModel;
use App\Model\RolePermission;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Psr\Container\ContainerInterface;

#[Command('permission:sync')]
class PermissionSyncCommand extends HyperfCommand
{
    #[Inject]
    protected ContainerInterface $container;

    public function handle(): void
    {
        $permissions = [];

        //所有 Controller
        $controllers = AnnotationCollector::getClassesByAnnotation(Controller::class);

        //Mapping 映射
        $mappingMap = [
            GetMapping::class => 'GET',
            PostMapping::class => 'POST',
            PutMapping::class => 'PUT',
            DeleteMapping::class => 'DELETE',
        ];
        foreach ($controllers as $class => $controllerAnnotation) {

            //prefix
            $prefix = trim($controllerAnnotation->prefix ?? '', '/');

            //获取类所有方法
            $methods = get_class_methods($class);

            foreach ($methods as $method) {

                //方法上的所有注解
                $methodAnnotations = AnnotationCollector::getClassMethodAnnotation($class, $method);

                //没有 Permission 直接跳过
                if (!isset($methodAnnotations[Permission::class])) {
                    continue;
                }

                /** @var Permission $perm */
                $perm = $methodAnnotations[Permission::class];

                $httpMethod = null;
                $path = null;

                //查找 Mapping
                foreach ($mappingMap as $mappingClass => $methodName) {

                    if (!isset($methodAnnotations[$mappingClass])) {
                        continue;
                    }

                    $mapping = $methodAnnotations[$mappingClass];

                    $httpMethod = $methodName;
                    $path = $prefix . '/' . trim($mapping->path ?? '', '/');

                    break;
                }

                //“必须有路由才算权限”
                if (!$httpMethod || !$path) {
                    continue;
                }

                $permissions[] = [
                    'name' => $perm->name,
                    'description' => $perm->description,
                    'method' => $httpMethod,
                    'path' => $path,
                ];
            }
        }

        // 收集所有 name
        $names = array_column($permissions, 'name');

        if (empty($names)) {
            $this->error('未扫描到任何权限，已中止删除操作！');
            return;
        }

        //一次性查出所有（包括软删除）
        $existing = PermissionModel::withTrashed()
            ->whereIn('name', $names)
            ->get()
            ->keyBy('name'); // 关键：转成 name => model

        // 1. 更新 / 新增
        foreach ($permissions as $permission) {

            /** @var PermissionModel|null $model */
            $model = $existing[$permission['name']] ?? null;

            if ($model) {
                if ($model->trashed()) {
                    $model->restore();
                }
                $model->update($permission);
            } else {
                PermissionModel::create($permission);
            }
        }


        // 2. 删除数据库中多余的
        $deletePermissions = PermissionModel::query()
            ->whereNotIn('name', $names)
            ->pluck('id');

        if ($deletePermissions->isNotEmpty()) {

            // 删除角色权限关联
            RolePermission::query()
                ->whereIn('permission_id', $deletePermissions)
                ->delete();

            // 删除权限（软删除）
            PermissionModel::query()
                ->whereIn('id', $deletePermissions)
                ->delete();
        }
    }
}