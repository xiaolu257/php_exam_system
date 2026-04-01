<?php

namespace App\Command;

use App\Annotation\Permission;
use App\Model\Permission as PermissionModel;
use App\Model\RolePermission;
use App\Service\PermissionLogService;
use Hyperf\Collection\Collection;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\DbConnection\Db;
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
    #[Inject]
    protected PermissionLogService $logService;

    private function diffPermission(array $old, array $new): array
    {
        // 只对比核心字段（非常重要）
        $fields = ['name', 'description', 'method', 'path'];

        $oldFiltered = array_intersect_key($old, array_flip($fields));
        $newFiltered = array_intersect_key($new, array_flip($fields));

        return array_diff_assoc($newFiltered, $oldFiltered);
    }

    private function formatPermissionData(array $data): array
    {
        return [
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'method' => $data['method'] ?? null,
            'path' => $data['path'] ?? null,
        ];
    }

    private function collectPermissionsFromAnnotation(): array
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
        return $permissions;
    }

    private function createOrUpdatePermission(Collection $existing, array $permissions, string $batchId): void
    {
        // 1. 更新 / 新增
        foreach ($permissions as $permission) {
            /** @var PermissionModel|null $model */
            $model = $existing[$permission['name']] ?? null;

            if ($model) {
                $oldData = $this->formatPermissionData($model->toArray());
                //处理恢复
                if ($model->trashed()) {
                    $model->restore();
                    $this->logService->record(
                        'restore',
                        $permission['name'],
                        $oldData,
                        $permission,
                        $batchId
                    );
                    $model->refresh();
                }
                //处理更新（只在有变化时）
                $diff = $this->diffPermission($oldData, $permission);

                if (!empty($diff)) {
                    $model->update($permission);
                    $this->logService->record(
                        'update',
                        $permission['name'],
                        $oldData,
                        $permission,
                        $batchId
                    );
                }
            } else {
                PermissionModel::create($permission);
                $this->logService->record(
                    'create',
                    $permission['name'],
                    null,
                    $permission,
                    $batchId
                );
            }
        }
    }

    private function deleteInexistencePermission(Collection $existing, array $names, string $batchId): void
    {
        $existingActiveNames = $existing
            ->filter(fn($m) => !$m->trashed())
            ->keys()
            ->toArray();

        $toDeleteNames = array_diff($existingActiveNames, $names);

        if (!empty($toDeleteNames)) {

            $models = PermissionModel::whereIn('name', $toDeleteNames)
                ->get(['id', 'name', 'description', 'method', 'path']);

            $deleteIds = $models->pluck('id');

            // 先删除关联
            RolePermission::query()
                ->whereIn('permission_id', $deleteIds)
                ->delete();

            // 再删除权限
            PermissionModel::query()
                ->whereIn('id', $deleteIds)
                ->delete();

            // 最后记录日志（更严谨）
            foreach ($models as $model) {

                $this->logService->record(
                    'delete',
                    $model->name,
                    [
                        'name' => $model->name,
                        'description' => $model->description,
                        'method' => $model->method,
                        'path' => $model->path,
                    ],
                    null,
                    $batchId
                );
            }
        }
    }

    public function handle(): void
    {
        $permissions = $this->collectPermissionsFromAnnotation();
        // 收集所有 name
        $names = array_column($permissions, 'name');

        if (empty($names)) {
            $this->error('未扫描到任何权限，已中止删除操作！');
            return;
        }
        //var_dump($names);
        Db::transaction(function () use ($permissions, $names) {
            $batchId = uniqid('perm_sync_', true);

            //一次性查出所有（包括软删除）
            $existing = PermissionModel::withTrashed()
                ->get(['id', 'name', 'description', 'method', 'path', 'deleted_at'])
                ->keyBy('name'); // 关键：转成 name => model
            //创建或更新权限
            $this->createOrUpdatePermission($existing, $permissions, $batchId);
            // 删除数据库中没有相关注解的权限
            $this->deleteInexistencePermission($existing, $names, $batchId);
        });

    }
}