<?php

declare(strict_types=1);

namespace App\Controller\RBAC;

use App\Annotation\PublicAPI;
use App\Model\UserRole;
use App\Request\UserRoleRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'user-role')]
class UserRoleController
{

    #[GetMapping('')]
    #[PublicAPI]
    #[Scene(UserRoleRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(UserRoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
        ];
        if (isset($fieldMap[$searchField])) {
            $searchField = $fieldMap[$searchField];
        }
        if (isset($fieldMap[$orderBy])) {
            $orderBy = $fieldMap[$orderBy];
        }
        $paginator = UserRole::query()
            ->leftJoin('users', 'users.id', '=', 'user_roles.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'user_roles.role_id')
            ->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue),
                function (Builder $query) use ($searchField, $searchValue) {
                    $query->where($searchField, 'like', "%{$searchValue}%");
                })
            ->paginate(15, [
                'user_roles.id',
                'user_roles.user_id',
                'users.username',
                'users.nickname',
                'user_roles.role_id',
                'roles.code as role_code',
                'roles.description as role_description',
                'user_roles.created_at'
            ], 'page', $page);
        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[PublicAPI]
    #[Scene(UserRoleRequest::SCENE_ADD)]
    public function add(UserRoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $singleChoiceQuestion = new UserRole();
        $singleChoiceQuestion->user_id = $validatedData['user_id'];
        $singleChoiceQuestion->role_id = $validatedData['role_id'];
        $singleChoiceQuestion->save();
        return $response->json(['msg' => '新增权限分配信息成功']);
    }

    #[DeleteMapping('')]
    #[PublicAPI]
    #[Scene(UserRoleRequest::SCENE_DELETE)]
    public function delete(UserRoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = UserRole::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = UserRole::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条权限分配信息数据"
        ]);
    }

}
