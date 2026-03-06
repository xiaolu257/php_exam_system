<?php

declare(strict_types=1);

namespace App\Controller;

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
    #[Scene(ShortAnswerQuestionRequest::SCENE_GET_ONE_PAGE_SHORT_ANSWER_QUESTIONS)]
    public function getOnePageShortAnswerQuestions(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(ShortAnswerQuestionRequest::SCENE_ADD_SHORT_ANSWER_QUESTIONS)]
    public function addShortAnswerQuestions(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(ShortAnswerQuestionRequest::SCENE_UPDATE_SHORT_ANSWER_QUESTIONS)]
    public function updateShortAnswerQuestions(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
    #[Scene(ShortAnswerQuestionRequest::SCENE_DELETE_SHORT_ANSWER_QUESTIONS)]
    public function deleteShortAnswerQuestions(ShortAnswerQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        ShortAnswerQuestion::query()->whereIn('id', $ids)->delete();
        return $response->json(['msg' => '删除简答题成功']);
    }

}
