<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Model\TrueFalseQuestion;
use App\Request\TrueFalseQuestionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'true-false-question')]
class TrueFalseQuestionController
{
    #[GetMapping('')]
    #[Permission('trueFalseQuestion:paginate', '获取判断题题分页数据，支持模糊查询')]
    #[Scene(TrueFalseQuestionRequest::SCENE_GET_ONE)]
    public function paginate(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = TrueFalseQuestion::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'content', 'correct_answer', 'created_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Permission('trueFalseQuestion:add', '新增判断题')]
    #[Scene(TrueFalseQuestionRequest::SCENE_ADD)]
    public function add(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $content = $validatedData['content'];
        $correctAnswer = $validatedData['correct_answer'];


        $trueFalseQuestion = new TrueFalseQuestion();
        $trueFalseQuestion->content = $content;
        $trueFalseQuestion->correct_answer = $correctAnswer;
        $trueFalseQuestion->save();

        return $response->json(['msg' => '新增判断题成功']);
    }

    #[PutMapping('')]
    #[Permission('trueFalseQuestion:update', '修改判断题')]
    #[Scene(TrueFalseQuestionRequest::SCENE_UPDATE)]
    public function update(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $content = $validatedData['content'] ?? null;
        $correctAnswer = $validatedData['correct_answer'] ?? null;

        $trueFalseQuestion = TrueFalseQuestion::query()->find($id);
        if (!$trueFalseQuestion) {
            return $response->json(['msg' => '判断题不存在'])->withStatus(404);
        }

        if ($content) {
            $trueFalseQuestion->content = $content;
        }

        if ($correctAnswer || $correctAnswer === 0) {
            $trueFalseQuestion->correct_answer = $correctAnswer;
        }

        if (!$trueFalseQuestion->isDirty()) {
            return $response->json(['msg' => '未检测到任何有效修改']);
        }
        $trueFalseQuestion->save();

        return $response->json(['msg' => '修改判断题成功']);
    }

    #[DeleteMapping('')]
    #[Permission('trueFalseQuestion:delete', '(批量)删除判断题题')]
    #[Scene(TrueFalseQuestionRequest::SCENE_DELETE)]
    public function delete(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = TrueFalseQuestion::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = TrueFalseQuestion::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条判断题数据"
        ]);
    }

}
