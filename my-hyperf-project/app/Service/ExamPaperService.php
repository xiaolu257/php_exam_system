<?php

namespace App\Service;


use App\Model\Exam;
use App\Model\ExamPaper;
use App\Model\ExamPaperQuestion;
use App\Model\ExamUserAnswer;
use App\Model\MultipleChoiceQuestion;
use App\Model\ShortAnswerQuestion;
use App\Model\SingleChoiceQuestion;
use App\Model\TrueFalseQuestion;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ExamPaperService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('ExamPaperService', 'mysql');
    }

    public function generateRandomQuestions(int $examPaperId,
                                            int $singleCount,
                                            int $multipleCount,
                                            int $trueFalseCount,
                                            int $shortAnswerCount): void
    {
        $insertData = [];
        $sortOrder = 1;

        /**
         * 单选题
         * @var SingleChoiceQuestion[] $singleQuestions
         */
        $singleQuestions = SingleChoiceQuestion::query()->inRandomOrder()->limit($singleCount)
            ->get(['id', 'content', 'options', 'correct_answer']);

        foreach ($singleQuestions as $q) {
            $insertData[] = [
                'exam_paper_id' => $examPaperId,
                'question_type' => 'single',
                'question_id' => $q->id,
                'score' => 2,
                'sort_order' => $sortOrder++,
                'question_snapshot' => json_encode([
                    'content' => $q->content,
                    'options' => $q->options,
                    'correct_answer' => $q->correct_answer,
                ], JSON_UNESCAPED_UNICODE),
            ];
        }

        /**
         * 多选题
         * @var MultipleChoiceQuestion[] $multipleQuestions
         */
        $multipleQuestions = MultipleChoiceQuestion::query()->inRandomOrder()->limit($multipleCount)
            ->get(['id', 'content', 'options', 'correct_answer']);

        foreach ($multipleQuestions as $q) {
            $insertData[] = [
                'exam_paper_id' => $examPaperId,
                'question_type' => 'multiple',
                'question_id' => $q->id,
                'score' => 4,
                'sort_order' => $sortOrder++,
                'question_snapshot' => json_encode([
                    'content' => $q->content,
                    'options' => $q->options,
                    'correct_answer' => $q->correct_answer,
                ], JSON_UNESCAPED_UNICODE),
            ];
        }

        /**
         * 判断题
         * @var TrueFalseQuestion[] $trueFalseQuestions
         */
        $trueFalseQuestions = TrueFalseQuestion::query()->inRandomOrder()->limit($trueFalseCount)
            ->get(['id', 'content', 'correct_answer']);

        foreach ($trueFalseQuestions as $q) {
            $insertData[] = [
                'exam_paper_id' => $examPaperId,
                'question_type' => 'true_false',
                'question_id' => $q->id,
                'score' => 1,
                'sort_order' => $sortOrder++,
                'question_snapshot' => json_encode([
                    'content' => $q->content,
                    'correct_answer' => $q->correct_answer,
                ], JSON_UNESCAPED_UNICODE),
            ];
        }

        // 简答题
        /**
         * 简答题
         * @var ShortAnswerQuestion[] $shortQuestions
         */
        $shortQuestions = ShortAnswerQuestion::query()->inRandomOrder()->limit($shortAnswerCount)
            ->get(['id', 'content', 'reference_answer']);

        foreach ($shortQuestions as $q) {
            $insertData[] = [
                'exam_paper_id' => $examPaperId,
                'question_type' => 'short_answer',
                'question_id' => $q->id,
                'score' => 5,
                'sort_order' => $sortOrder++,
                'question_snapshot' => json_encode([
                    'content' => $q->content,
                    'reference_answer' => $q->reference_answer,
                ], JSON_UNESCAPED_UNICODE),
            ];
        }

        // 批量插入
        ExamPaperQuestion::query()->insert($insertData);
    }

    public function judge(string $type, mixed $userAnswer, mixed $correctAnswer): int
    {
        return match ($type) {
            'single' => $userAnswer === $correctAnswer ? 2 : 0,
            'multiple' => self::judgeMultiple($userAnswer, $correctAnswer),
            'true_false' => $userAnswer === $correctAnswer ? 1 : 0,
            default => 0,
        };
    }

    private function judgeMultiple($userAnswer, $correctAnswer): int
    {
        return count($correctAnswer) === count($userAnswer)
        && empty(array_diff($correctAnswer, $userAnswer))
        && empty(array_diff($userAnswer, $correctAnswer))
            ? 4 : 0;
    }


    public function submitExamPaper(int               $examId,
                                    int               $userId,
                                    array             $userSubmitAnswers,
                                    ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
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

            $SubmitAnswers[$index]['score'] = $this->judge(
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
}
