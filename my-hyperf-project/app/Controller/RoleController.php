<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Role;
use App\Request\RoleRequest;
use App\Service\MenuService;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'role')]
class RoleController
{
    #[Inject]
    protected MenuService $menuService;


    #[GetMapping('selector')]
    //#[Permission('menu:add', '新增角色')]
    public function rolesSelector(ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $data = Role::query()->get([
            'id as value',
            Db::raw("CONCAT(code, '（', description, '）') as label")
        ]);;
        return $response->json($data->toArray());
    }

    #[GetMapping('')]
    //#[Permission('menu:paginate', '获取角色分页数据，支持模糊查询')]
    #[Scene(RoleRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(RoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = Role::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'code', 'description', 'created_at', 'updated_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    //#[Permission('menu:add', '新增角色')]
    #[Scene(RoleRequest::SCENE_ADD)]
    public function add(RoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $singleChoiceQuestion = new Role();
        $singleChoiceQuestion->code = $validatedData['code'];
        $singleChoiceQuestion->description = $validatedData['description'];
        $singleChoiceQuestion->save();
        return $response->json(['msg' => '新增角色成功']);
    }

    #[PutMapping('')]
    //#[Permission('menu:update', '更新角色')]
    #[Scene(RoleRequest::SCENE_UPDATE)]
    public function update(RoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $code = $validatedData['code'] ?? null;
        $description = $validatedData['description'] ?? null;

        $role = Role::query()->find($id);
        if (!$role) {
            return $response->json(['msg' => '角色不存在'])->withStatus(404);
        }
        if ($code) {
            $role->code = $code;
        }
        if ($description) {
            $role->description = $description;
        }
        $role->save();
        return $response->json(['msg' => '修改角色成功']);
    }

    #[DeleteMapping('')]
    //#[Permission('menu:delete', '(批量)删除角色')]
    #[Scene(RoleRequest::SCENE_DELETE)]
    public function delete(RoleRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = Role::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = Role::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条角色数据"
        ]);
    }

}
