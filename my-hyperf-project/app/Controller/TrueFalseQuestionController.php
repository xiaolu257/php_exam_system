<?php

declare(strict_types=1);

namespace App\Controller;

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
    #[Scene(TrueFalseQuestionRequest::SCENE_GET_ONE_PAGE_TRUE_FALSE_QUESTIONS)]
    public function getOnePageTrueFalseQuestions(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(TrueFalseQuestionRequest::SCENE_ADD_TRUE_FALSE_QUESTIONS)]
    public function addTrueFalseQuestions(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(TrueFalseQuestionRequest::SCENE_UPDATE_TRUE_FALSE_QUESTIONS)]
    public function updateTrueFalseQuestions(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(TrueFalseQuestionRequest::SCENE_DELETE_TRUE_FALSE_QUESTIONS)]
    public function deleteTrueFalseQuestions(TrueFalseQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
