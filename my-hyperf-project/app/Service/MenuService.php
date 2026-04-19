<?php

namespace App\Service;

use App\Model\Menu;

class MenuService
{
    public function willFormCycle(int $currentId, int $newParentId): bool
    {
        // 1. 允许设为顶级
        if ($newParentId === 0) {
            return false;
        }

        // 2. 不能指向自己
        if ($currentId === $newParentId) {
            return true;
        }

        // 3. 向上查父链
        $visited = [];

        while ($newParentId != 0) {

            // 防御：避免已有脏数据导致死循环
            if (isset($visited[$newParentId])) {
                return true;
            }
            $visited[$newParentId] = true;

            if ($newParentId === $currentId) {
                return true; // ❗形成环
            }

            $parent = Menu::query()
                ->where('id', $newParentId)
                ->value('parent_id');

            if (!$parent) {
                break;
            }

            $newParentId = $parent;
        }

        return false;
    }

    /**
     * 获取菜单树
     */
    public function getMenuTree(): array
    {
        // 1. 查全部菜单（排除软删除）
        $menus = Menu::query()
            ->orderBy('sort')
            ->get(['id', 'parent_id', 'name'])
            ->toArray();

        // 2. 转树
        $tree = $this->buildTree($menus, 0);

        // 3. 在最前面添加"无"选项
        array_unshift($tree, [
            'value' => 0,
            'label' => '无'
        ]);

        return $tree;
    }

    /**
     * 构建树结构
     */
    private function buildTree(array $menus, int $parentId): array
    {
        $tree = [];

        foreach ($menus as $menu) {
            if ((int)$menu['parent_id'] === $parentId) {

                $children = $this->buildTree($menus, (int)$menu['id']);

                $node = [
                    'value' => (int)$menu['id'],
                    'label' => $menu['name'],
                ];

                if (!empty($children)) {
                    $node['children'] = $children;
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }
}