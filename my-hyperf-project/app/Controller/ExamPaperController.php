<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Exam;
use App\Model\ExamPaper;
use App\Model\ExamPaperQuestion;
use App\Model\ExamUserAnswer;
use App\Request\ExamPaperRequest;
use App\Service\ExamPaperService;
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
        //1.先确定用户考试次数是否用尽
        $exam = Exam::query()->find($examId);
        if (!$exam) {
            return $response->json(['msg' => '未找到相关考试信息'])->withStatus(404);
        }
        $examPaper = ExamPaper::query()->where('id', $exam->exam_paper_id)->first();
        if (!$examPaper) {
            return $response->json(['msg' => '未找到相关试卷'])->withStatus(404);
        }
        $examCount = Exam::query()->where('user_id', $userId)->where('exam_paper_id', $exam->exam_paper_id)->count();
        if ($examCount >= $examPaper->max_attempts) {
            return $response->json(['msg' => '参考次数已达上限，无法再次交卷'])->withStatus(409);
        }
        //2.获取试卷的详细信息

        //3.自动批改客观题
        $SubmitAnswers = [];
        foreach ($userSubmitAnswers as $index => $userSubmitAnswer) {
            if ($index === 'single_questions') {
                foreach ($userSubmitAnswer as $u) {
                    $u['question_type'] = 'single';
                    $SubmitAnswers[] = $u;
                }
            } else if ($index === 'multiple_questions') {
                foreach ($userSubmitAnswer as $u) {
                    $u['question_type'] = 'multiple';
                    $SubmitAnswers[] = $u;
                }
            } else if ($index === 'true_false_questions') {
                foreach ($userSubmitAnswer as $u) {
                    $u['question_type'] = 'true_false';
                    $SubmitAnswers[] = $u;
                }
            } else if ($index === 'short_answer_questions') {
                foreach ($userSubmitAnswer as $u) {
                    $u['question_type'] = 'short_answer';
                    $SubmitAnswers[] = $u;
                }
            }
        }
        $questionIds = array_column($SubmitAnswers, 'id');

        $questions = ExamPaperQuestion::query()
            ->whereIn('id', $questionIds)
            ->get()
            ->keyBy('id');
        foreach ($SubmitAnswers as $index => $submitAnswer) {
            $examPaperQuestion = $questions[$submitAnswer['id']] ?? null;
            if (!$examPaperQuestion) {
                return $response->json(['msg' => '未找到相关试卷题目'])->withStatus(404);
            }
            if ($submitAnswer['question_type'] === 'single') {
                if ($submitAnswer['answer'] === $examPaperQuestion->question_snapshot['correct_answer']) {
                    $SubmitAnswers[$index]['score'] = 2;
                } else {
                    $SubmitAnswers[$index]['score'] = 0;
                }
            } else if ($submitAnswer['question_type'] === 'multiple') {
                $correct_answer = $examPaperQuestion->question_snapshot['correct_answer'];
                $answer = $submitAnswer['answer'];
                if (
                    count($correct_answer) === count($answer) &&
                    empty(array_diff($correct_answer, $answer)) &&
                    empty(array_diff($answer, $correct_answer))
                ) {
                    $SubmitAnswers[$index]['score'] = 4;
                } else {
                    $SubmitAnswers[$index]['score'] = 0;
                }
            } else if ($submitAnswer['question_type'] === 'true_false') {
                if ($submitAnswer['answer'] === $examPaperQuestion->question_snapshot['correct_answer']) {
                    $SubmitAnswers[$index]['score'] = 1;
                } else {
                    $SubmitAnswers[$index]['score'] = 0;
                }
            } else {
                $SubmitAnswers[$index]['score'] = 0;
            }
        }
        //4.录入考试答案
        Db::transaction(function () use ($SubmitAnswers, $examId) {
            foreach ($SubmitAnswers as $index => $submitAnswer) {
                $SubmitAnswers[$index]['exam_id'] = $examId;
                $SubmitAnswers[$index]['exam_paper_question_id'] = $SubmitAnswers[$index]['id'];
                $SubmitAnswers[$index]['answer'] = json_encode($submitAnswer['answer'], JSON_UNESCAPED_UNICODE);
                unset($SubmitAnswers[$index]['id']);
            }
            ExamUserAnswer::query()->insert($SubmitAnswers);
        });
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
