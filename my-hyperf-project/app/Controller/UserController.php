<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Model\UserToken;
use App\Request\UserRequest;
use App\Service\ImageService;
use App\Util\TokenUtil;
use Exception;
use Firebase\JWT\ExpiredException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Annotation\Scene;

#[Controller]
class UserController
{
    #[Inject]
    protected ImageService $imageService;

    #[GetMapping('index')]
    public function index(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        return $response->raw('Hello Hyperf!');
    }

    #[PostMapping('register')]
    #[Scene(UserRequest::SCENE_REGISTER)]
    public function register(UserRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $data = $request->validated();
        if (User::where('username', $data['username'])->value('id')) {
            return $response->json(['msg' => '管理员账号重复'])->withStatus(422);
        }
        $image = $request->file('avatar');
        if ($image instanceof UploadedFile) {
            $avatar_url = $this->imageService->saveUserAvatar($image, $data['username']);
            if (!$avatar_url) {
                return $response->json(['msg' => '由于头像保存失败，因此新增管理员失败，请稍后重试'])->withStatus(422);
            }
            $data['avatar_url'] = $avatar_url;
        }
        User::create(array_filter([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'nickname' => $data['nickname'] ?? null,
            'avatar_url' => $data['avatar_url'] ?? null,
        ], fn($v) => !is_null($v)));
        return $response->json(['msg' => '新增管理员成功']);
    }

    #[PostMapping('login')]
    #[Scene(UserRequest::SCENE_LOGIN)]
    public function login(UserRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $validated = $request->validated();
        // ✅ 获取设备指纹（来自请求头）
        $validated['fingerprint'] = $request->header('Fingerprint', '666');
        $model = User::select(['id', 'nickname', 'password', 'role', 'status', 'avatar_url'])->where('username', '=', $validated['username'])->first();
        if (!$model || !password_verify($validated['password'], $model->password)) {
            return $response->json(['msg' => '账号或密码错误，登录失败'])->withStatus(401);
        }
        if ($model->status != 1) {
            return $response->json(['msg' => '账号已被禁用，登录失败'])->withStatus(403);
        }
        // 创建 access_token & refresh_token
        $accessToken = TokenUtil::createToken($model->id, $validated['fingerprint']);      // 1小时
        $refreshToken = TokenUtil::createToken($model->id, $validated['fingerprint'], 3600 * 24 * 7); // 7天
        // 设置过期时间
        $expiresTime = date('Y-m-d H:i:s', time() + 3600);  // 当前时间 + 1小时（access_token过期时间）
        $refreshExpiresTime = date('Y-m-d H:i:s', time() + 86400 * 7);  // 当前时间 + 7天（refresh_token过期时间）
        // 更新 token 数据
        UserToken::where('user_id', $model->id)->delete(); // 清理旧记录
        UserToken::create([
            'user_id' => $model->id,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_time' => $expiresTime,  // 设置 Access Token 过期时间
            'refresh_expires_time' => $refreshExpiresTime,  // 设置 Refresh Token 过期时间
        ]);
        return $response->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]);
    }

    #[PostMapping('validateAdminToken')]
    public function validateAdminToken(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        try {
            // 获取前端传来的 Authorization 头部
            $authHeader = $request->header('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return $response->json(['msg' => '缺少或非法的 Authorization 头部'])->withStatus(422);
            }
            $postFingerprint = $request->header('Fingerprint');
            if (!$postFingerprint) {
                return $response->json(['msg' => '设备指纹不能为空'])->withStatus(422);
            }
            $accessToken = substr($authHeader, 7); // 去掉 Bearer 空格
            // 解码 Token（会校验签名和 exp）
            $decoded = TokenUtil::decodeToken($accessToken);
            $adminId = $decoded->uid ?? null;
            $fingerprint = $decoded->fingerprint ?? null;

            if (!$adminId || !$fingerprint) {
                return $response->json(['msg' => 'Token 数据不完整'])->withStatus(422);
            }

            if (!password_verify($postFingerprint, $fingerprint)) {
                return $response->json(['msg' => '设备指纹不一致'])->withStatus(401);
            }

            // 进一步验证数据库中的 token 是否存在
            $model = UserToken::where('user_id', $adminId)
                ->select(['access_token'])
                ->first();
            if (!$model) {
                return $response->json(['msg' => '不存在相关管理员'])->withStatus(404);
            }
            if (!password_verify($accessToken, $model->access_token)) {
                return $response->json(['msg' => '非法Token,code'])->withStatus(401);
            }
            // 可选：更新 token 的最后使用时间
            $model->save(['update_time' => date('Y-m-d H:i:s')]);

            // 加载管理员数据
            $admin = User::find($adminId);
            if (!$admin) {
                return $response->json(['success' => false, 'error' => '管理员不存在']);
            }

            return $response->json([
                'success' => true,
                'userData' => [
                    'id' => $admin->id,
                    'username' => $admin->username,
                    'nickname' => $admin->nickname,
                    'role' => $admin->role,
                    'avatar_url' => $admin->avatar_url,
                ]
            ]);
        } catch (ExpiredException $e) {
            return $response->json(['msg' => 'Token 已过期'])->withStatus(401);
        } catch (Exception $e) {
            return $response->json(['msg' => 'Token 无效：' . $e->getMessage()])->withStatus(401);
        }
    }
}
