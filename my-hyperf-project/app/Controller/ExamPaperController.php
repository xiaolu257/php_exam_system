<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Exam;
use App\Model\ExamPaper;
use App\Model\ExamPaperQuestion;
use App\Model\ExamUserAnswer;
use App\Request\ExamPaperRequest;
use App\Service\ExamPaperService;
use Carbon\Carbon;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;
use RuntimeException;

#[Controller(prefix: 'exam-paper')]
class ExamPaperController
{
    #[Inject]
    protected ExamPaperService $examPaperService;

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

    #[GetMapping('{id:\d+}')]
    public function getOne(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $id = $request->route('id', 0);

        $exam = ExamPaper::query()
            ->where('id', $id)
            ->first(['id', 'title', 'description', 'duration', 'total_score', 'start_time', 'end_time', 'max_attempts']);

        if (!$exam) {
            return $response->json(['message' => '考试不存在'])->withStatus(404);
        }
        $counts = ExamPaperQuestion::query()
            ->where('exam_paper_id', $id)
            ->selectRaw('question_type, COUNT(*) as count')
            ->groupBy('question_type')
            ->pluck('count', 'question_type');

        $exam->single_count = $counts['single'] ?? 0;
        $exam->multiple_count = $counts['multiple'] ?? 0;
        $exam->true_false_count = $counts['true_false'] ?? 0;
        $exam->short_answer_count = $counts['short_answer'] ?? 0;

        return $response->json($exam);
    }

    #[GetMapping('{id:\d+}/questions')]
    public function getOneDetail(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $id = $request->route('id', 0);

        $exam = Exam::query()->find($id);
        if (!$exam) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }
        $examPaper = ExamPaper::query()
            ->find($exam->exam_paper_id, ['id', 'title', 'description', 'duration', 'total_score', 'start_time', 'end_time', 'max_attempts']);

        if (!$examPaper) {
            return $response->json(['message' => '未找到相关试卷'])->withStatus(404);
        }
        $counts = ExamPaperQuestion::query()
            ->where('exam_paper_id', $examPaper->id)
            ->selectRaw('question_type, COUNT(*) as count')
            ->groupBy('question_type')
            ->pluck('count', 'question_type');

        $examPaper->single_count = $counts['single'] ?? 0;
        $examPaper->multiple_count = $counts['multiple'] ?? 0;
        $examPaper->true_false_count = $counts['true_false'] ?? 0;
        $examPaper->short_answer_count = $counts['short_answer'] ?? 0;

        $examPaper->questions = ExamPaperQuestion::query()
            ->where('exam_paper_id', $examPaper->id)
            ->orderBy('sort_order')
            ->get(['id', 'question_type', 'score', 'sort_order', 'question_snapshot']);
        //移除正确答案，避免暴露在前端
        foreach ($examPaper->questions as $question) {
            $snapshot = $question->question_snapshot;

            if (in_array($question->question_type, ['single', 'multiple', 'true_false'])) {
                unset($snapshot['correct_answer']);
            } else if ($question->question_type === 'short_answer') {
                unset($snapshot['reference_answer']);
            }

            $question->question_snapshot = $snapshot;
        }
        return $response->json($examPaper);
    }

    #[PostMapping('{id:\d+}/submit')]
    #[Scene(ExamPaperRequest::SCENE_SUBMIT_EXAM_PAPER)]
    public function submitExamPaper(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examId = $validatedData['exam_id'];
        $userSubmitAnswers = $validatedData['answers'];
        $userId = $request->getAttribute('user_id');

        // 1. 查询考试信息并校验交卷权限
        $exam = Exam::query()->find($examId);
        if (!$exam) {
            return $response->json(['msg' => '未找到相关考试信息'])->withStatus(404);
        }
        if ($exam->user_id !== $userId) {
            return $response->json(['msg' => '检测到交卷者与考生信息不一致，请诚信考试，禁止替考！'])->withStatus(409);
        }
        if ($exam->status !== 'ongoing') {
            return $response->json(['msg' => '考试不在“进行中”，禁止交卷'])->withStatus(409);
        }

        $examPaper = ExamPaper::query()->find($exam->exam_paper_id);
        if (!$examPaper) {
            return $response->json(['msg' => '未找到相关试卷'])->withStatus(404);
        }

        // 2. 校验考试时间
        $now = Carbon::now();
        $examStart = $exam->start_time;
        $deadline = $examStart->copy()->addMinutes($examPaper->duration);
        if (!$now->betweenExcluded($examStart, $deadline)) {
            return $response->json(['msg' => '不在考试时间范围内，禁止交卷'])->withStatus(403);
        }

        // 3. 扁平化用户答案
        $typeMap = [
            'single_questions' => 'single',
            'multiple_questions' => 'multiple',
            'true_false_questions' => 'true_false',
            'short_answer_questions' => 'short_answer',
        ];

        $SubmitAnswers = [];
        foreach ($userSubmitAnswers as $key => $answers) {
            $type = $typeMap[$key] ?? null;
            if (!$type) continue;
            foreach ($answers as $a) {
                $a['question_type'] = $type;
                $SubmitAnswers[] = $a;
            }
        }

        // 4. 获取试卷题目信息
        $questionIds = array_column($SubmitAnswers, 'id');
        $questions = ExamPaperQuestion::query()
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');

        // 5. 批改答案（纯 PHP 操作，不在事务内）
        foreach ($SubmitAnswers as $index => $submitAnswer) {
            $examPaperQuestion = $questions[$submitAnswer['id']] ?? null;
            if (!$examPaperQuestion) {
                return $response->json(['msg' => '未找到相关试卷题目'])->withStatus(404);
            }
            if ($submitAnswer['question_type'] !== $examPaperQuestion['question_type']) {
                return $response->json(['msg' => '所提交的答案的题型与考试题目不一致'])->withStatus(422);
            }

            $correctAnswerName = $submitAnswer['question_type'] === 'short_answer'
                ? 'reference_answer'
                : 'correct_answer';

            $SubmitAnswers[$index]['score'] = $this->examPaperService->judge(
                $submitAnswer['question_type'],
                $submitAnswer['answer'],
                $examPaperQuestion->question_snapshot[$correctAnswerName]
            );
        }

        // 6. 事务：检查重复交卷 + 插入答案
        try {
            Db::transaction(function () use ($SubmitAnswers, $examId) {
                if (ExamUserAnswer::query()->where('exam_id', $examId)->exists()) {
                    throw new RuntimeException('该场考试已存在交卷记录，禁止重复交卷');
                }

                $data = array_map(fn($item) => [
                    'exam_id' => $examId,
                    'exam_paper_question_id' => $item['id'],
                    'question_type' => $item['question_type'],
                    'answer' => json_encode($item['answer'], JSON_UNESCAPED_UNICODE),
                    'score' => $item['score'],
                ], $SubmitAnswers);

                ExamUserAnswer::query()->insert($data);
            });
        } catch (RuntimeException $e) {
            return $response->json(['msg' => $e->getMessage()])->withStatus(409);
        }

        return $response->json(['msg' => '交卷成功']);
    }

    #[PostMapping('')]
    #[Scene(ExamPaperRequest::SCENE_ADD)]
    public function add(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        Db::transaction(function () use ($validatedData, &$examPaper) {
            $singleCount = $validatedData['single_count'];
            $multipleCount = $validatedData['multiple_count'];
            $trueFalseCount = $validatedData['true_false_count'];
            $shortAnswerCount = $validatedData['short_answer_count'];
            // 创建试卷
            $examPaper = new ExamPaper();
            $examPaper->title = $validatedData['title'];
            if (!empty($validatedData['description'])) {
                $examPaper->description = $validatedData['description'];
            }
            $examPaper->duration = $validatedData['duration'];
            $examPaper->start_time = $validatedData['start_time'];
            $examPaper->end_time = $validatedData['end_time'];
            if (!empty($validatedData['max_attempts'])) {
                $examPaper->max_attempts = $validatedData['max_attempts'];
            }
            $examPaper->total_score = $singleCount * 2 + $multipleCount * 4 + $trueFalseCount * 1 + $shortAnswerCount * 5;
            $examPaper->save();

            $this->examPaperService->generateRandomQuestions($examPaper->id, $singleCount, $multipleCount, $trueFalseCount, $shortAnswerCount);
        });
        return $response->json(['msg' => '新增试卷成功']);
    }

    #[PutMapping('')]
    #[Scene(ExamPaperRequest::SCENE_UPDATE)]
    public function update(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];

        $examPaper = ExamPaper::query()->find($id, ['id']);
        if (!$examPaper) {
            return $response->json(['msg' => '试卷不存在'])->withStatus(404);
        }
        $exams = Exam::query()->where('exam_paper_id', $examPaper->id)->pluck('id')->toArray();
        if (!empty($exams)) {
            return $response->json(['msg' => '已有考生参加考试，不可更改试卷信息'])->withStatus(409);
        }
        if (!empty($validatedData['title'])) {
            $examPaper->title = $validatedData['title'];
        }
        if (!empty($validatedData['description'])) {
            $examPaper->description = $validatedData['description'];
        }
        if (!empty($validatedData['duration'])) {
            $examPaper->duration = $validatedData['duration'];
        }
        if (!empty($validatedData['start_time'])) {
            $examPaper->start_time = $validatedData['start_time'];
        }
        if (!empty($validatedData['end_time'])) {
            $examPaper->end_time = $validatedData['end_time'];
        }
        if (!empty($validatedData['max_attempts'])) {
            $examPaper->max_attempts = $validatedData['max_attempts'];
        }

        if (!$examPaper->isDirty()) {
            return $response->json(['msg' => '未检测到任何有效修改']);
        }
        $examPaper->save();

        return $response->json(['msg' => '修改试卷成功']);
    }

    #[DeleteMapping('')]
    #[Scene(ExamPaperRequest::SCENE_DELETE)]
    public function delete(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $ids = $validatedData['ids'];

        //找到实际存在的试卷ID
        $existingIds = ExamPaper::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->toArray();

        if (empty($existingIds)) {
            return $response->json(['msg' => '所选试卷不存在'])->withStatus(404);
        }

        //查出“已经被考试使用”的试卷ID
        $usedPaperIds = Exam::query()
            ->whereIn('exam_paper_id', $existingIds)
            ->distinct()
            ->pluck('exam_paper_id')
            ->toArray();

        //可删除的ID = 总ID - 已使用ID
        $deletableIds = array_diff($existingIds, $usedPaperIds);

        //执行删除
        $count = 0;
        if (!empty($deletableIds)) {
            $count = ExamPaper::query()
                ->whereIn('id', $deletableIds)
                ->delete();
        }

        // 返回结果（重点：提示哪些不能删）
        $blocked_ids = array_values($usedPaperIds);

        $msg = "成功删除 $count 条试卷数据";

        if (!empty($blocked_ids)) {
            $idsStr = implode(',', $blocked_ids);
            $msg = "成功删除 $count 条试卷数据，但以下试卷已被使用，无法删除：[$idsStr]";
        }

        return $response->json(['msg' => $msg]);
    }

}
