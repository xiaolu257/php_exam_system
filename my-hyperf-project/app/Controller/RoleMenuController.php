<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Menu;
use App\Model\RoleMenu;
use App\Request\RoleMenuRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'role-menu')]
class RoleMenuController
{
    #[GetMapping('menus-selector')]
    public function menusSelector(ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $data = Menu::query()->get([
            'id as value',
            Db::raw("CONCAT(code, '（', name, '）') as label")
        ]);;
        return $response->json($data->toArray());
    }

    #[GetMapping('')]
    #[Scene(RoleMenuRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(RoleMenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $fieldMap = [
            'role_code' => 'roles.code',
            'menu_name' => 'menus.name',
            'menu_code' => 'menus.code',
        ];
        if (isset($fieldMap[$searchField])) {
            $searchField = $fieldMap[$searchField];
        }
        if (isset($fieldMap[$orderBy])) {
            $orderBy = $fieldMap[$orderBy];
        }
        $paginator = RoleMenu::query()
            ->leftJoin('roles', 'roles.id', '=', 'role_menus.role_id')
            ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
            ->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue),
                function (Builder $query) use ($searchField, $searchValue) {
                    $query->where($searchField, 'like', "%{$searchValue}%");
                })
            ->paginate(15, ['role_menus.id',
                'role_menus.role_id',
                'roles.code as role_code',
                'role_menus.menu_id',
                'menus.name as menu_name',
                'menus.code as menu_code',
                'role_menus.created_at'
            ], 'page', $page);
        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Scene(RoleMenuRequest::SCENE_ADD)]
    public function add(RoleMenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $singleChoiceQuestion = new RoleMenu();
        $singleChoiceQuestion->role_id = $validatedData['role_id'];
        $singleChoiceQuestion->menu_id = $validatedData['menu_id'];
        $singleChoiceQuestion->save();
        return $response->json(['msg' => '新增菜单分配信息成功']);
    }

    #[DeleteMapping('')]
    #[Scene(RoleMenuRequest::SCENE_DELETE)]
    public function delete(RoleMenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = RoleMenu::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = RoleMenu::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条菜单分配信息数据"
        ]);
    }

}
