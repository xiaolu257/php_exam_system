<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Model\SingleChoiceQuestion;
use App\Request\SingleChoiceQuestionRequest;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller(prefix: 'single-choice-question')]
class SingleChoiceQuestionController
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
    #[Permission('singleChoiceQuestion:paginate', '获取单选题分页数据，支持模糊查询')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_GET_ONE)]
    public function paginate(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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
            ->paginate(15, ['id', 'content', 'options', 'correct_answer', 'created_at'], 'page', $page);

        return $response->json(['data' => $paginator->items(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]);
    }

    #[PostMapping('')]
    #[Permission('singleChoiceQuestion:add', '新增单选题')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_ADD)]
    public function add(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
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

        if (!array_key_exists($correctAnswer, $optionsMap)) {
            return $response->json(['msg' => '正确答案不在选项中,请重新核对'])->withStatus(422);
        }

        $singleChoiceQuestion = new SingleChoiceQuestion();
        $singleChoiceQuestion->content = $content;
        $singleChoiceQuestion->options = $optionsMap;
        $singleChoiceQuestion->correct_answer = $correctAnswer;
        $singleChoiceQuestion->save();

        return $response->json(['msg' => '新增单选题成功']);
    }

    #[PutMapping('')]
    #[Permission('singleChoiceQuestion:update', '更新单选题')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_UPDATE)]
    public function update(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];
        $content = $validatedData['content'] ?? null;
        $options = $validatedData['options'] ?? null;
        $correctAnswer = $validatedData['correct_answer'] ?? null;

        $singleChoiceQuestion = SingleChoiceQuestion::query()->find($id);
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
    #[Permission('singleChoiceQuestion:delete', '(批量)删除单选题')]
    #[Scene(SingleChoiceQuestionRequest::SCENE_DELETE)]
    public function delete(SingleChoiceQuestionRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $ids = $validatedData['ids'];

        $existingIds = SingleChoiceQuestion::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        $count = SingleChoiceQuestion::query()->whereIn('id', $existingIds)->delete();

        return $response->json([
            'msg' => "成功删除 $count 条单选题数据"
        ]);
    }

}
