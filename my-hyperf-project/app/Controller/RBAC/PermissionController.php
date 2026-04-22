<?php

declare(strict_types=1);

namespace App\Controller\RBAC;

use App\Annotation\AuthOnly;
use App\Model\Permission;
use App\Request\PermissionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'permission')]
class PermissionController
{
    #[GetMapping('selector')]
    #[AuthOnly]
    public function selector(ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $data = Permission::query()->get([
            'id as value',
            Db::raw("CONCAT(code, '（', description, '）') as label")
        ]);;
        return $response->json($data->toArray());
    }

    #[GetMapping('')]
    #[AuthOnly]
    #[Scene(PermissionRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(PermissionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = Permission::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'code', 'description', 'path', 'method', 'created_at', 'updated_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }
}
