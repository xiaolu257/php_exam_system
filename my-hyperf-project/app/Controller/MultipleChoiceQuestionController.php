<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\MultipleChoiceQuestion;
use App\Request\MultipleChoiceQuestionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'multiple-choice-question')]
class MultipleChoiceQuestionController
{
    /**
     * 将数字下标0,1,2,... 映射为 A,B,C,...
     * @param int $index
     * @return string
     */
    private function indexToOptionKey(int $index): string
    {
        return chr(ord('A') + $index);
    }

    #[GetMapping('')]
    #[Scene(MultipleChoiceQuestionRequest::SCENE_GET_ONE_PAGE_MULTIPLE_CHOICE_QUESTIONS)]
    public function getOnePageMultipleChoiceQuestions(MultipleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = MultipleChoiceQuestion::query()->orderBy($orderBy, $orderDirection)
            ->when(!is_null($searchField) && !is_null($searchValue), function (Builder $query) use ($searchField, $searchValue) {
                $query->where($searchField, 'like', "%{$searchValue}%");
            })
            ->paginate(15, ['id', 'content', 'options', 'correct_answer', 'created_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Scene(MultipleChoiceQuestionRequest::SCENE_ADD_MULTIPLE_CHOICE_QUESTIONS)]
    public function addMultipleChoiceQuestions(MultipleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $content = $validatedData['content'];
        $options = $validatedData['options'];
        $correctAnswer = $validatedData['correct_answer'];

        // 将选项数组转换为 A,B,C,... 的键值对
        $optionsMap = [];
        foreach ($options as $index => $value) {
            $optionsMap[$this->indexToOptionKey($index)] = $value;
        }

        //检测正确答案的每个选项是否在题目选项中
        foreach ($correctAnswer as $answer) {
            if (!array_key_exists($answer, $optionsMap)) {
                return $response->json(['msg' => '正确答案中存在无效选项,请重新核对'])->withStatus(422);
            }
        }
        $multipleChoiceQuestion = new MultipleChoiceQuestion();
        $multipleChoiceQuestion->content = $content;
        $multipleChoiceQuestion->options = $optionsMap;
        $multipleChoiceQuestion->correct_answer = $correctAnswer;
        $multipleChoiceQuestion->save();

        return $response->json(['msg' => '新增多选题成功']);
    }

    #[PutMapping('')]
    #[Scene(MultipleChoiceQuestionRequest::SCENE_UPDATE_MULTIPLE_CHOICE_QUESTIONS)]
    public function updateMultipleChoiceQuestions(MultipleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $content = $validatedData['content'] ?? null;
        $options = $validatedData['options'] ?? null;
        $correctAnswer = $validatedData['correct_answer'] ?? null;

        if ($correctAnswer) {
            //判断正确选项是否是升序的字母数组
            $sorted = $correctAnswer;
            sort($sorted);

            if ($sorted !== $correctAnswer) {
                return $response->json(['msg' => '正确答案必须按字母升序排列'])->withStatus(422);
            }
        }

        $multipleChoiceQuestion = MultipleChoiceQuestion::query()->find($id);
        if (!$multipleChoiceQuestion) {
            return $response->json(['msg' => '多选题不存在'])->withStatus(404);
        }

        if ($content) {
            $multipleChoiceQuestion->content = $content;
        }

        if ($options) {
            // 将选项数组转换为 A,B,C,... 的键值对
            $optionsMap = [];
            foreach ($options as $index => $value) {
                $optionsMap[$this->indexToOptionKey($index)] = $value;
            }
            if ($correctAnswer) {
                foreach ($correctAnswer as $answer) {
                    if (!array_key_exists($answer, $optionsMap)) {
                        return $response->json(['msg' => '对于新的选项，新的正确答案中存在无效选项,请重新核对'])->withStatus(422);
                    }
                }
                $multipleChoiceQuestion->correct_answer = $correctAnswer;
            } else {
                foreach ($multipleChoiceQuestion->correct_answer as $answer) {
                    if (!array_key_exists($answer, $optionsMap)) {
                        return $response->json(['msg' => '对于新的选项，旧的正确答案中存在无效选项,请重新核对'])->withStatus(422);
                    }
                }
            }
            $multipleChoiceQuestion->options = $optionsMap;
        } else {
            if ($correctAnswer) {
                foreach ($correctAnswer as $answer) {
                    if (!array_key_exists($answer, $multipleChoiceQuestion->options)) {
                        return $response->json(['msg' => '对于旧的选项，新的正确答案中存在无效选项,请重新核对'])->withStatus(422);
                    }
                }
                $multipleChoiceQuestion->correct_answer = $correctAnswer;
            }
        }

        if (!$multipleChoiceQuestion->isDirty()) {
            return $response->json(['msg' => '未检测到任何有效修改']);
        }
        $multipleChoiceQuestion->save();

        return $response->json(['msg' => '修改多选题成功']);
    }

    #[DeleteMapping('')]
    #[Scene(MultipleChoiceQuestionRequest::SCENE_DELETE_MULTIPLE_CHOICE_QUESTIONS)]
    public function deleteMultipleChoiceQuestions(MultipleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        MultipleChoiceQuestion::query()->whereIn('id', $ids)->delete();
        return $response->json(['msg' => '删除多选题成功']);
    }

}
