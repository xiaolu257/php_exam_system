<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\ExamPaper;
use App\Request\ExamPaperRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'exam-paper')]
class ExamPaperController
{
    #[GetMapping('test')]
    public function test(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        return $response->json(['data' => '']);
    }

    #[GetMapping('')]
    #[Scene(ExamPaperRequest::SCENE_GET_ONE_PAGE)]
    public function getOnePage(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = ExamPaper::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'title', 'description', 'duration', 'total_score', 'start_time', 'end_time', 'max_attempts', 'created_at', 'updated_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Scene(ExamPaperRequest::SCENE_ADD)]
    public function add(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $examPaper = new ExamPaper();
        $examPaper->title = $validatedData['title'];
        if (!empty($validatedData['description'])) {
            $examPaper->description = $validatedData['description'];
        }
        $examPaper->duration = $validatedData['duration'];
        $examPaper->total_score = $validatedData['total_score'];
        $examPaper->start_time = $validatedData['start_time'];
        $examPaper->end_time = $validatedData['end_time'];
        if (!empty($validatedData['max_attempts'])) {
            $examPaper->max_attempts = $validatedData['max_attempts'];
        }
        $examPaper->save();

        return $response->json(['msg' => '新增试卷成功']);
    }

    #[PutMapping('')]
    #[Scene(ExamPaperRequest::SCENE_UPDATE)]
    public function update(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $content = $validatedData['content'] ?? null;
        $options = $validatedData['options'] ?? null;
        $correctAnswer = $validatedData['correct_answer'] ?? null;

        $singleChoiceQuestion = ExamPaper::query()->find($id);
        if (!$singleChoiceQuestion) {
            return $response->json(['msg' => '单选题不存在'])->withStatus(404);
        }

        if ($content) {
            $singleChoiceQuestion->content = $content;
        }

        if ($options) {
            // 将选项数组转换为 A,B,C,... 的键值对
            $optionsMap = [];
            foreach ($options as $index => $value) {
                $optionsMap[$this->indexToOptionKey($index)] = $value;
            }
            if ($correctAnswer) {
                if (!array_key_exists($correctAnswer, $optionsMap)) {
                    return $response->json(['msg' => '新的正确答案不在新的选项中,请重新核对'])->withStatus(422);
                }
                $singleChoiceQuestion->correct_answer = $correctAnswer;
            } else {
                if (!array_key_exists($singleChoiceQuestion->correct_answer, $optionsMap)) {
                    return $response->json(['msg' => '旧的正确答案不在新的选项中,请重新核对'])->withStatus(422);
                }
            }
            $singleChoiceQuestion->options = $optionsMap;
        } else {
            if ($correctAnswer) {
                if (!array_key_exists($correctAnswer, $singleChoiceQuestion->options)) {
                    return $response->json(['msg' => '新的正确答案不在旧的选项中,请重新核对'])->withStatus(422);
                }
                $singleChoiceQuestion->correct_answer = $correctAnswer;
            }
        }

        if (!$singleChoiceQuestion->isDirty()) {
            return $response->json(['msg' => '未检测到任何有效修改']);
        }
        $singleChoiceQuestion->save();

        return $response->json(['msg' => '修改单选题成功']);
    }

    #[DeleteMapping('')]
    #[Scene(ExamPaperRequest::SCENE_DELETE)]
    public function delete(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = ExamPaper::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = ExamPaper::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条单选题数据"
        ]);
    }

}
