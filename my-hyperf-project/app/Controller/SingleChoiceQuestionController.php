<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\SingleChoiceQuestion;
use App\Request\SingleChoiceQuestionRequest;
use App\Service\ImageService;
use Hyperf\Database\Model\Builder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'single-choice-question')]
class SingleChoiceQuestionController
{
    #[Inject]
    protected ImageService $imageService;

    #[GetMapping('test')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_GET_ONE_PAGE_SINGLE_CHOICE_QUESTIONS)]
    public function test(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        return $response->json(['data' => '']);
    }

    #[GetMapping('')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_GET_ONE_PAGE_SINGLE_CHOICE_QUESTIONS)]
    public function getOnePageSingleChoiceQuestions(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();
        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;
        $paginator = SingleChoiceQuestion::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'content', 'options', 'correct_answer', 'score', 'created_at'], 'page', $page);
        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }
}
