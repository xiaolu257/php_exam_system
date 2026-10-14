<?php

declare(strict_types=1);

namespace App\Controller;

use App\Annotation\Permission;
use App\Middleware\Helper\MiddlewareContext;
use App\Model\Exam;
use App\Model\ExamAdmission;
use App\Model\ExamPaper;
use App\Model\ExamPaperQuestion;
use App\Model\User;
use App\Request\ExamPaperRequest;
use App\Service\ExamPaperService;
use Hyperf\Database\Exception\QueryException;
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
    #[Inject]
    protected MiddlewareContext $middlewareContext;

    #[GetMapping('')]
    #[Permission('examPaper:paginate', '获取试卷分页数据，支持模糊查询')]
    #[Scene(ExamPaperRequest::SCENE_GET_ONE_PAGE)]
    public function paginate(ExamPaperRequest  $request,
                             ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;

        $paginator = ExamPaper::query()
                              ->orderBy($orderBy, $orderDirection)
                              ->when(!is_null($searchField) && !is_null($searchValue),
                                  function (Builder $query) use ($searchField, $searchValue) {
                                      $query->where($searchField, 'like', "%{$searchValue}%");
                                  })
                              ->paginate(15, [
                                  'id',
                                  'title',
                                  'description',
                                  'duration',
                                  'total_score',
                                  'start_time',
                                  'end_time',
                                  'max_attempts',
                                  'created_at',
                                  'updated_at',
                              ], 'page', $page);

        return $response->json([
            'data'      => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ]);
    }

    #[GetMapping('getMyExam')]
    #[Permission('examPaper:getMyExam', '学生获取与本人相关的试卷分页数据，支持模糊查询')]
    #[Scene(ExamPaperRequest::SCENE_GET_ONE_PAGE)]
    public function getMyExam(ExamPaperRequest  $request,
                              ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;
        $userId = $this->middlewareContext::getUserId();

        $paperIds = ExamAdmission::query()
                                 ->where('user_id', $userId)
                                 ->pluck('exam_paper_id');
        $paginator = ExamPaper::query()
                              ->orderBy($orderBy, $orderDirection)
                              ->when(!is_null($searchField) && !is_null($searchValue),
                                  function (Builder $query) use ($searchField, $searchValue) {
                                      $query->where($searchField, 'like', "%{$searchValue}%");
                                  })
                              ->whereIn('id', $paperIds)
                              ->paginate(15, [
                                  'id',
                                  'title',
                                  'description',
                                  'duration',
                                  'total_score',
                                  'start_time',
                                  'end_time',
                                  'max_attempts',
                                  'created_at',
                                  'updated_at',
                              ], 'page', $page);

        return $response->json([
            'data'      => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ]);
    }

    #[GetMapping('getMyExamPapers')]
    #[Permission('examPaper:getMyExamPapers', '管理员获取本人发布的试卷分页数据，支持模糊查询')]
    #[Scene(ExamPaperRequest::SCENE_GET_MY_EXAM_PAPERS)]
    public function getMyExamPapers(ExamPaperRequest  $request,
                                    ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;
        $userId = $this->middlewareContext::getUserId();

        $paginator = ExamPaper::query()
                              ->orderBy($orderBy, $orderDirection)
                              ->when(!is_null($searchField) && !is_null($searchValue),
                                  function (Builder $query) use ($searchField, $searchValue) {
                                      $query->where($searchField, 'like', "%{$searchValue}%");
                                  })
                              ->where('publisher_id', $userId)
                              ->paginate(15, [
                                  'id',
                                  'title',
                                  'description',
                                  'duration',
                                  'total_score',
                                  'start_time',
                                  'end_time',
                                  'max_attempts',
                                  'created_at',
                                  'updated_at',
                              ], 'page', $page);

        return $response->json([
            'data'      => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ]);
    }

    #[GetMapping('getExamAdmission')]
    #[Permission('examPaper:getExamAdmission', '管理员获取本人发布的试卷的考试资格分页数据，支持模糊查询')]
    #[Scene(ExamPaperRequest::SCENE_GET_EXAM_ADMISSION)]
    public function getExamAdmission(ExamPaperRequest  $request,
                                     ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $examPaperId = (int)$validated['id'];
        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()
                              ->where('id', $examPaperId)
                              ->first();
        if (empty($examPaper)) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }
        if ($examPaper->publisher_id !== $userId) {
            return $response->json(['message' => '非试卷发布者无法获取相关考试资格信息'])->withStatus(403);
        }

        //搜索字段映射，主要解决多表字段同名问题
        $searchFieldMap = [
            'id'     => 'exam_admissions.id',
            'status' => 'exam_admissions.status',
        ];

        $paginator = ExamAdmission::query()
                                  ->orderBy($orderBy, $orderDirection)
                                  ->when(!is_null($searchField) && !is_null($searchValue) && isset($searchFieldMap[$searchField]),
                                      function (Builder $query) use ($searchFieldMap, $searchField, $searchValue) {
                                          $query->where(
                                              $searchFieldMap[$searchField],
                                              'like',
                                              "%{$searchValue}%"
                                          );
                                      }
                                  )
                                  ->where('exam_paper_id', $examPaperId)
                                  ->leftJoin('users', 'users.id', '=', 'exam_admissions.user_id')
                                  ->paginate(15, [
                                      'exam_admissions.id',
                                      'exam_admissions.user_id',
                                      'users.username',
                                      'users.nickname',
                                      'admission_method',
                                      'exam_admissions.status',
                                      'exam_admissions.created_at',
                                      'exam_admissions.updated_at',
                                  ], 'page', $page);

        return $response->json([
            'data'      => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ]);
    }

    #[GetMapping('getUsersWithoutExamAdmission')]
    #[Permission('examPaper:getUsersWithoutExamAdmission', '管理员获取指定试卷（需本人发布）未获得授权的用户分页数据，支持模糊查询')]
    #[Scene(ExamPaperRequest::SCENE_GET_USERS_WITHOUT_EXAM_ADMISSION)]
    public function getUsersWithoutExamAdmission(ExamPaperRequest  $request,
                                                 ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();

        $examPaperId = (int)$validated['id'];
        $page = (int)$validated['page'];
        $orderBy = $validated['orderBy'] ?? 'id';
        $orderDirection = $validated['orderDirection'] ?? 'asc';
        $searchField = $validated['searchField'] ?? null;
        $searchValue = $validated['searchValue'] ?? null;
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()
                              ->where('id', $examPaperId)
                              ->first();
        if (empty($examPaper)) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }
        if ($examPaper->publisher_id !== $userId) {
            return $response->json(['message' => '非试卷发布者无法获取相关信息'])->withStatus(403);
        }

        $authorizedUserIds = ExamAdmission::query()
                                          ->where(['exam_paper_id' => $examPaperId])
                                          ->pluck('user_id')
                                          ->toArray();

        $paginator = User::query()
                         ->orderBy($orderBy, $orderDirection)
                         ->when(!is_null($searchField) && !is_null($searchValue),
                             function (Builder $query) use ($searchField, $searchValue) {
                                 $query->where($searchField, 'like', "%{$searchValue}%");
                             })
                         ->whereNotIn('id', $authorizedUserIds)
                         ->paginate(15, [
                             'id',
                             'username',
                             'nickname',
                             'avatar_url',
                         ], 'page', $page);

        return $response->json([
            'data'      => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ]);
    }

    #[GetMapping('{id:\d+}')]
    #[Permission('examPaper:getOne', '考生考试前，获取考试的简要信息')]
    public function getOne(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $id = $request->route('id', 0);

        $exam = ExamPaper::query()
                         ->where('id', $id)
                         ->first([
                             'id',
                             'title',
                             'description',
                             'duration',
                             'total_score',
                             'start_time',
                             'end_time',
                             'max_attempts',
                         ]);

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

    #[GetMapping('{exam_id:\d+}/questions')]
    #[Permission('exam:getExamDetail', '考生获取考试信息，包括试题')]
    public function getExamDetail(RequestInterface  $request,
                                  ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $exam_id = (int)$request->route('exam_id', 0);

        return $this->examPaperService->getExamDetail($exam_id, $response);
    }

    #[GetMapping('{exam_paper_id:\d+}/preview')]
    #[Permission('examPaper:getExamPaperDetailWithAnswer', '获取试卷信息+答案')]
    public function getExamPaperDetailWithAnswer(RequestInterface  $request,
                                                 ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $exam_paper_id = (int)$request->route('exam_paper_id', 0);

        return $this->examPaperService->getExamPaperDetail($exam_paper_id, $response);
    }

    #[PostMapping('{exam_paper_id:\d+}/start')]
    #[Permission('exam:start', '考生开始考试，创建考试信息')]
    public function startExam(ExamPaperRequest  $request,
                              ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $userId = $this->middlewareContext->getUserId();
        $examPaperId = (int)$request->route('exam_paper_id', 0);
        return $this->examPaperService->startExam($userId, $examPaperId, $response);
    }

    #[PostMapping('{exam_id:\d+}/submit')]
    #[Permission('exam:submit', '考生交卷，创建交卷信息')]
    #[Scene(ExamPaperRequest::SCENE_SUBMIT_EXAM_PAPER)]
    public function submitExamPaper(ExamPaperRequest  $request,
                                    ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examId = (int)$request->route('exam_id', 0);
        $userSubmitAnswers = $validatedData['answers'];
        $userId = $this->middlewareContext->getUserId();
        return $this->examPaperService->submitExamPaper($examId, $userId, $userSubmitAnswers, $response);
    }

    #[PostMapping('')]
    #[Permission('examPaper:add', '新增试卷')]
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

            $this->examPaperService->generateRandomQuestions($examPaper->id, $singleCount, $multipleCount,
                $trueFalseCount, $shortAnswerCount);
        });
        return $response->json(['msg' => '新增试卷成功']);
    }

    #[PutMapping('')]
    #[Permission('examPaper:update', '修改试卷')]
    #[Scene(ExamPaperRequest::SCENE_UPDATE)]
    public function update(ExamPaperRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();

        $id = $validatedData['id'];

        $examPaper = ExamPaper::query()
                              ->find($id, ['id']);
        if (!$examPaper) {
            return $response->json(['msg' => '试卷不存在'])->withStatus(404);
        }
        $exams = Exam::query()->where('exam_paper_id', $examPaper->id)
                     ->pluck('id')
                     ->toArray();
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
    #[Permission('examPaper:delete', '(批量)删除试卷')]
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
            $count = ExamPaper::query()->whereIn('id', $deletableIds)->delete();
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

    #[PostMapping('revokeExamAdmission')]
    #[Permission('examPaper:revokeExamAdmission', '(批量)取消考试资格')]
    #[Scene(ExamPaperRequest::SCENE_CHANGE_EXAM_ADMISSION)]
    public function revokeExamAdmission(ExamPaperRequest  $request,
                                        ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examPaperId = $validatedData['id'];
        $ids = $validatedData['ids'];
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()->where('id', $examPaperId)->first();
        if (empty($examPaper)) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }
        if ($examPaper->publisher_id !== $userId) {
            return $response->json(['message' => '非试卷发布者无法进行相关操作'])->withStatus(403);
        }
        $count = ExamAdmission::query()
                              ->whereIn('id', $ids)
                              ->where('exam_paper_id', $examPaperId)
                              ->where('status', '!=', 'revoked')
                              ->update(['status' => 'revoked']);

        return $response->json(['msg' => "成功“取消资格” $count 条考试授权数据"]);
    }

    #[PostMapping('restoreExamAdmission')]
    #[Permission('examPaper:restoreExamAdmission', '(批量)恢复已撤销的考试资格')]
    #[Scene(ExamPaperRequest::SCENE_CHANGE_EXAM_ADMISSION)]
    public function restoreExamAdmission(ExamPaperRequest  $request,
                                         ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examPaperId = $validatedData['id'];
        $ids = $validatedData['ids'];
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()
                              ->where('id', $examPaperId)
                              ->first();
        if (empty($examPaper)) {
            return $response
                ->json(['message' => '未找到相关考试信息'])
                ->withStatus(404);
        }
        if ($examPaper->publisher_id !== $userId) {
            return $response
                ->json(['message' => '非试卷发布者无法进行相关操作'])
                ->withStatus(403);
        }
        $count = ExamAdmission::query()
                              ->whereIn('id', $ids)
                              ->where('exam_paper_id', $examPaperId)
                              ->where('status', '!=', 'authorized')
                              ->update(['status' => 'authorized']);

        return $response->json(['msg' => "成功“重新授权” $count 条考试授权数据"]);
    }

    #[PostMapping('addExamAdmission')]
    #[Permission('examPaper:addExamAdmission', '(批量)添加考试资格')]
    #[Scene(ExamPaperRequest::SCENE_CHANGE_EXAM_ADMISSION)]
    public function addExamAdmission(ExamPaperRequest  $request,
                                     ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examPaperId = $validatedData['id'];
        $ids = $validatedData['ids'];
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()->where('id', $examPaperId)->first();
        if (empty($examPaper)) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }
        if ($examPaper->publisher_id !== $userId) {
            return $response->json(['message' => '非试卷发布者无法进行相关操作'])->withStatus(403);
        }
        $insertData = [];
        foreach ($ids as $i) {
            $insertData[] = [
                'exam_paper_id'    => $examPaperId,
                'user_id'          => $i,
                'admission_method' => 'manual',
                'status'           => 'authorized',
            ];
        }
        try {
            $ok = ExamAdmission::query()->insert($insertData);
            if ($ok) {
                return $response->json(['msg' => "批量添加考生成功！"]);
            } else {
                return $response->json(['msg' => "批量添加考生失败"])->withStatus(409);
            }
        } catch (QueryException) {
            //如果exam_paper_id和user_id联合唯一键冲突，数据库会拒绝执行整条语句，不会添加任何记录
            return $response->json(['msg' => "批量添加考生失败"])->withStatus(409);
        }

    }

    #[GetMapping('getExamAdmissionStatistics')]
    #[Permission('examPaper:getExamAdmissionStatistics', '获取考试资格统计')]
    #[Scene(ExamPaperRequest::SCENE_ONLY_ID)]
    public function getExamAdmissionStatistics(
        ExamPaperRequest  $request,
        ResponseInterface $response
    ): \Psr\Http\Message\ResponseInterface
    {
        $validatedData = $request->validated();
        $examPaperId = $validatedData['id'];
        $userId = $this->middlewareContext::getUserId();

        $examPaper = ExamPaper::query()->where('id', $examPaperId)->first();

        if (empty($examPaper)) {
            return $response->json(['message' => '未找到相关考试信息'])->withStatus(404);
        }

        if ($examPaper->publisher_id !== $userId) {
            return $response->json(['message' => '非试卷发布者无法获取相关信息'])->withStatus(403);
        }

        // 当前有效资格的考生 ID
        $authorizedUserIds = ExamAdmission::query()
                                          ->where('exam_paper_id', $examPaperId)
                                          ->where('status', 'authorized')
                                          ->pluck('user_id');

        // 当前有效资格人数
        $authorizedUserCount = $authorizedUserIds->count();

        // 当前有效资格中，已参加考试的去重考生人数
        $participatedUserCount = 0;

        if ($authorizedUserCount > 0) {
            $participatedUserCount = Exam::query()
                                         ->where('exam_paper_id', $examPaperId)
                                         ->whereIn('user_id', $authorizedUserIds)
                                         ->distinct()
                                         ->count('user_id');
        }

        // 当前有效资格中，尚未参加考试的人数
        $notParticipatedUserCount = max(0, $authorizedUserCount - $participatedUserCount);

        // 当前已取消资格人数
        $revokedUserCount = ExamAdmission::query()
                                         ->where('exam_paper_id', $examPaperId)
                                         ->where('status', 'revoked')
                                         ->count();

        return $response->json([
            'authorizedUserCount'      => $authorizedUserCount,
            'participatedUserCount'    => $participatedUserCount,
            'notParticipatedUserCount' => $notParticipatedUserCount,
            'revokedUserCount'         => $revokedUserCount,
        ]);
    }
}
