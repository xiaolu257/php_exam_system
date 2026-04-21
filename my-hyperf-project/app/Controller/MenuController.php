<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Menu;
use App\Request\MenuRequest;
use App\Service\MenuService;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'menu')]
class MenuController
{
    #[Inject]
    protected MenuService $menuService;


    #[GetMapping('')]
    //#[Permission('menu:paginate', '获取菜单分页数据，支持模糊查询')]
    #[Scene(MenuRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(MenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = Menu::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'parent_id', 'name', 'code', 'sort', 'created_at', 'updated_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[GetMapping('menu-tree-selector')]
    public function getMenuTree(ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        return $response->json($this->menuService->getMenuTree());
    }

    #[PostMapping('')]
    //#[Permission('menu:add', '新增菜单')]
    #[Scene(MenuRequest::SCENE_ADD)]
    public function add(MenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $singleChoiceQuestion = new Menu();
        $singleChoiceQuestion->name = $validatedData['name'];
        $singleChoiceQuestion->code = $validatedData['code'];
        $singleChoiceQuestion->parent_id = $validatedData['parent_id'];
        $singleChoiceQuestion->sort = $validatedData['sort'];
        $singleChoiceQuestion->save();
        return $response->json(['msg' => '新增菜单成功']);
    }

    #[PutMapping('')]
    //#[Permission('menu:update', '更新菜单')]
    #[Scene(MenuRequest::SCENE_UPDATE)]
    public function update(MenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $name = $validatedData['name'] ?? null;
        $code = $validatedData['code'] ?? null;
        $parentId = $validatedData['parent_id'] ?? null;
        $sort = $validatedData['sort'] ?? null;

        $menu = Menu::query()->find($id);
        if (!$menu) {
            return $response->json(['msg' => '菜单不存在'])->withStatus(404);
        }
        if ($name) {
            $menu->name = $name;
        }
        if ($code) {
            $menu->code = $code;
        }
        if ($parentId || $parentId === 0) {

            if ($parentId !== 0) {
                $m = Menu::query()->find($parentId, ['id']);
                if (!$m) {
                    return $response->json(['msg' => '父菜单不存在'])->withStatus(404);
                }
            }

            // 🚨 防环检测
            if ($this->menuService->willFormCycle($id, $parentId)) {
                return $response->json(['msg' => '不能将父节点设置为自身或子节点（会形成环）'])->withStatus(422);
            }

            $menu->parent_id = $parentId;
        }
        if ($sort) {
            $menu->sort = $sort;
        }

        $menu->save();
        return $response->json(['msg' => '修改菜单成功']);
    }

    #[DeleteMapping('')]
    //#[Permission('menu:delete', '(批量)删除菜单')]
    #[Scene(MenuRequest::SCENE_DELETE)]
    public function delete(MenuRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = Menu::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = Menu::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条菜单数据"
        ]);
    }

}
