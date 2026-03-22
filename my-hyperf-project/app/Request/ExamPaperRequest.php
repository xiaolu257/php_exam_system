<?php

declare(strict_types=1);

namespace App\Request;

use App\Request\CustomerRules\SingleAnswerRule;
use Hyperf\Validation\Request\FormRequest;


class ExamPaperRequest extends FormRequest
{
    public const SCENE_GET_ONE_PAGE = 'getOnePage';
    public const SCENE_ADD = 'add';
    public const SCENE_UPDATE = 'update';
    public const SCENE_DELETE = 'delete';

    public const SCENE_SUBMIT_EXAM_PAPER = 'submitExamPaper';

    public function authorize(): bool
    {
        return true;
    }

    protected array $scenes = [
        self::SCENE_GET_ONE_PAGE => [
            'page' => 'required|integer|gt:0',
            'orderBy' => 'string|in:id,content,created_at',
            'orderDirection' => 'string|in:asc,desc',
            'searchField' => 'string|in:id,content,options',
            'searchValue' => 'required_with:searchField|string|max:100'
        ],

        self::SCENE_ADD => [
            'title' => 'required|string|max:255',
            'description' => 'filled|string|max:500',
            'duration' => 'required|integer:strict|gt:0',
            'single_count' => 'required|integer:strict|gt:0',
            'multiple_count' => 'required|integer:strict|gt:0',
            'true_false_count' => 'required|integer:strict|gt:0',
            'short_answer_count' => 'required|integer:strict|gt:0',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s',
            'max_attempts' => 'integer:strict|gt:0',
        ],

        self::SCENE_UPDATE => [
            'id' => 'required|integer:strict|gt:0',
            'title' => 'filled|string|max:255',
            'description' => 'filled|string|max:500',
            'duration' => 'integer:strict|gt:0',
            'start_time' => 'date_format:Y-m-d H:i:s',
            'end_time' => 'date_format:Y-m-d H:i:s',
            'max_attempts' => 'integer:strict|gt:0',
        ],

        self::SCENE_DELETE => [
            'ids' => 'required|array',
            'ids.*' => 'integer:strict|gt:0',
        ],

        self::SCENE_SUBMIT_EXAM_PAPER => [
            'single_questions' => 'present|array',
            'single_questions.*.id' => 'required|integer:strict|gt:0',
            //single_questions.*.answer,  ""为未作答
            'multiple_questions' => 'present|array',
            'multiple_questions.*.id' => 'required|integer:strict|gt:0',
            'multiple_questions.*.answer' => 'present|array',//[]为未作答
            'multiple_questions.*.answer.*' => 'filled|regex:/^[A-J]$/',
            'true_false_questions' => 'present|array',
            'true_false_questions.*.id' => 'required|integer:strict|gt:0',
            'true_false_questions.*.answer' => 'required|integer:strict|in:-1,0,1',//-1为未作答
            'short_answer_questions' => 'present|array',
            'short_answer_questions.*.id' => 'required|integer:strict|gt:0',
            'short_answer_questions.*.answer' => 'present|string|max:1000',//""为未作答
        ]
    ];

    public function rules(): array
    {
        if ($this->getScene() === self::SCENE_SUBMIT_EXAM_PAPER) {
            return [
                'single_questions.*.answer' => [
                    'present',
                    'string',
                    function ($attribute, $value, $fail) {
                        if ($value === '') {
                            return;
                        }

                        if (!preg_match('/^[A-J]$/', $value)) {
                            $fail('单选答案必须是 ""或A-J');
                        }
                    }
                ],
            ];

        }
        return [

        ];
    }

    public function attributes(): array
    {
        return [
            'page' => '页码',
            'orderBy' => '排序字段',
            'orderDirection' => '排序方向',
            'searchField' => '搜索字段',
            'searchValue' => '搜索值',
            'content' => '题目',
            'options' => '选项',
            'options.*' => '选项内容',
            'correct_answer' => '正确答案',
            'ids' => 'ID列表',
            'ids.*' => 'ID列表的每一项ID'
        ];
    }

    public function messages(): array
    {
        return [
            'content.filled' => ':attribute 不能为空',
            'options.*.filled' => ':attribute 不能为空',
            'correct_answer.index_in' => ':attribute 必须存在于 :field 中',
            'correct_answer.regex' => ':attribute 必须是 A 到 J 之间的字母',
            'correct_answer.filled' => ':attribute 不能为空',
        ];
    }
}
