<?php

declare(strict_types=1);

namespace App\Controller\RBAC;

use App\Annotation\AuthOnly;
use App\Model\RolePermission;
use App\Request\RolePermissionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'role-permission')]
class RolePermissionController
{

    #[GetMapping('')]
    #[AuthOnly]
    #[Scene(RolePermissionRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(RolePermissionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $fieldMap = [
            'role_code' => 'roles.code',
            'role_description' => 'roles.description',
            'permission_code' => 'permissions.code',
            'permission_description' => 'permissions.description',
        ];
        if (isset($fieldMap[$searchField])) {
            $searchField = $fieldMap[$searchField];
        }
        if (isset($fieldMap[$orderBy])) {
            $orderBy = $fieldMap[$orderBy];
        }
        $paginator = RolePermission::query()
            ->leftJoin('roles', 'roles.id', '=', 'role_permissions.role_id')
            ->leftJoin('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue),
                function (Builder $query) use ($searchField, $searchValue) {
                    $query->where($searchField, 'like', "%{$searchValue}%");
                })
            ->paginate(15, ['role_permissions.id',
                'role_permissions.role_id',
                'roles.description as role_description',
                'roles.code as role_code',
                'role_permissions.permission_id',
                'permissions.code as permission_code',
                'permissions.description as permission_description',
                'permissions.path',
                'permissions.method',
                'role_permissions.created_at'
            ], 'page', $page);
        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[AuthOnly]
    #[Scene(RolePermissionRequest::SCENE_ADD)]
    public function add(RolePermissionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $singleChoiceQuestion = new RolePermission();
        $singleChoiceQuestion->role_id = $validatedData['role_id'];
        $singleChoiceQuestion->permission_id = $validatedData['permission_id'];
        $singleChoiceQuestion->save();
        return $response->json(['msg' => '新增权限分配信息成功']);
    }

    #[DeleteMapping('')]
    #[AuthOnly]
    #[Scene(RolePermissionRequest::SCENE_DELETE)]
    public function delete(RolePermissionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = RolePermission::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = RolePermission::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条权限分配信息数据"
        ]);
    }

}
