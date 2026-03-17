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

        // 单选题
        $singleIds = SingleChoiceQuestion::query()->inRandomOrder()->limit($singleCount)
            ->pluck('id')->toArray();

        foreach ($singleIds as $id) {
            $insertData[] = [
                'paper_id' => $examPaperId,
                'question_type' => 'single',
                'question_id' => $id,
                'score' => 2,
                'sort_order' => $sortOrder++,
            ];
        }

        // 多选题
        $multipleIds = MultipleChoiceQuestion::query()->inRandomOrder()->limit($multipleCount)
            ->pluck('id')->toArray();

        foreach ($multipleIds as $id) {
            $insertData[] = [
                'paper_id' => $examPaperId,
                'question_type' => 'multiple',
                'question_id' => $id,
                'score' => 4,
                'sort_order' => $sortOrder++,
            ];
        }

        // 判断题
        $trueFalseIds = TrueFalseQuestion::query()->inRandomOrder()->limit($trueFalseCount)
            ->pluck('id')->toArray();

        foreach ($trueFalseIds as $id) {
            $insertData[] = [
                'paper_id' => $examPaperId,
                'question_type' => 'true_false',
                'question_id' => $id,
                'score' => 1,
                'sort_order' => $sortOrder++,
            ];
        }

        // 简答题
        $shortIds = ShortAnswerQuestion::query()->inRandomOrder()->limit($shortAnswerCount)
            ->pluck('id')->toArray();

        foreach ($shortIds as $id) {
            $insertData[] = [
                'paper_id' => $examPaperId,
                'question_type' => 'short_answer',
                'question_id' => $id,
                'score' => 5,
                'sort_order' => $sortOrder++,
            ];
        }

        // 批量插入
        ExamPaperQuestion::query()->insert($insertData);
    }
}
