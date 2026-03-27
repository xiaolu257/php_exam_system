<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Model\ShortAnswerQuestion;
use App\Request\ShortAnswerQuestionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'short-answer-question')]
class ShortAnswerQuestionController
{
    #[GetMapping('')]
    #[Permission('shortAnswerQuestion:paginate', '获取简答题分页数据，支持模糊查询')]
    #[Scene(ShortAnswerQuestionRequest::SCENE_GET_ONE)]
    public function paginate(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = ShortAnswerQuestion::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'content', 'reference_answer', 'created_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Permission('shortAnswerQuestion:add', '新增简答题')]
    #[Scene(ShortAnswerQuestionRequest::SCENE_ADD)]
    public function add(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $content = $validatedData['content'];
        $referenceAnswer = $validatedData['reference_answer'];


        $trueFalseQuestion = new ShortAnswerQuestion();
        $trueFalseQuestion->content = $content;
        $trueFalseQuestion->reference_answer = $referenceAnswer;
        $trueFalseQuestion->save();

        return $response->json(['msg' => '新增简答题成功']);
    }

    #[PutMapping('')]
    #[Permission('shortAnswerQuestion:update', '更新简答题')]
    #[Scene(ShortAnswerQuestionRequest::SCENE_UPDATE)]
    public function update(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $content = $validatedData['content'] ?? null;
        $referenceAnswer = $validatedData['reference_answer'] ?? null;

        $trueFalseQuestion = ShortAnswerQuestion::query()->find($id);
        if (!$trueFalseQuestion) {
            return $response->json(['msg' => '简答题不存在'])->withStatus(404);
        }

        if ($content) {
            $trueFalseQuestion->content = $content;
        }

        if ($referenceAnswer || $referenceAnswer === 0) {
            $trueFalseQuestion->reference_answer = $referenceAnswer;
        }

        if (!$trueFalseQuestion->isDirty()) {
            return $response->json(['msg' => '未检测到任何有效修改']);
        }
        $trueFalseQuestion->save();

        return $response->json(['msg' => '修改简答题成功']);
    }

    #[DeleteMapping('')]
    #[Permission('shortAnswerQuestion:delete', '(批量)删除简答题')]
    #[Scene(ShortAnswerQuestionRequest::SCENE_DELETE)]
    public function delete(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = ShortAnswerQuestion::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = ShortAnswerQuestion::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条简答题数据"
        ]);
    }

}
