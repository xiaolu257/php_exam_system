<?php

declare(strict_types=1);

namespace App\Controller\RBAC;

use App\Annotation\PublicAPI;
use App\Model\User;
use App\Request\UserRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'user')]
class UserController
{
    #[GetMapping('')]
    #[PublicAPI]
    #[Scene(UserRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(UserRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = User::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                if (is_numeric($searchValue)) {
                    $query->where($searchField, $searchValue);
                } else {
                    $query->where($searchField, 'like', "%{$searchValue}%");
                }
            })
            ->paginate(15, ['id', 'username', 'nickname', 'avatar_url', 'status', 'created_at', 'updated_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PutMapping('')]
    #[PublicAPI]
    #[Scene(UserRequest::SCENE_UPDATE)]
    public function update(UserRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $status = $validatedData['status'];

        $role = User::query()->find($id);
        if (!$role) {
            return $response->json(['msg' => '用户不存在'])->withStatus(404);
        }
        $role->status = $status;
        $role->save();
        return $response->json(['msg' => '修改用户信息成功']);
    }
}
