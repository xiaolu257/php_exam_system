<?php

namespace App\Service;


use App\Model\ExamPaperQuestion;
use App\Model\MultipleChoiceQuestion;
use App\Model\ShortAnswerQuestion;
use App\Model\SingleChoiceQuestion;
use App\Model\TrueFalseQuestion;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

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
}
