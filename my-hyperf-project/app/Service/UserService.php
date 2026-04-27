<?php

namespace App\Service;

use App\Model\User;
use App\Model\UserToken;
use App\Util\TokenUtil;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\Redis;

class UserService
{
    public function login(array $validated, string $fingerprint, ResponseInterface $response, Redis $redis): \Psr\Http\Message\ResponseInterface
    {
        // 1. 检查账号锁定状态
        $username = $validated['username'];
        $failKey = "login:fail:{$username}";
        $maxAttempts = 5;
        if ($redis->exists($failKey)) {

            $lockTTL = $redis->ttl($failKey);
            if ($lockTTL > 0 && $redis->get($failKey) >= $maxAttempts) {
                return $response->json(['msg' => "账号已锁定，请{$lockTTL}秒后再试"])->withStatus(401);
            }
        }

        // 4. 验证码验证
        $captchaKey = "captcha:{$fingerprint}";
        $expectedCaptcha = $redis->get($captchaKey);
        if (!$expectedCaptcha || $validated['captcha'] !== $expectedCaptcha) {
            // 验证码错误，保留原验证码（或选择删除）
            return $response->json(['msg' => '验证码错误'])->withStatus(422);
        }
        $redis->del($captchaKey); // 验证通过才删除

        // 5. 账号密码验证

        $model = User::select(['id', 'nickname', 'password', 'role', 'status', 'avatar_url'])
            ->where('username', '=', $username)->first();


        if (!$model || !password_verify($validated['password'], $model->password)) {

            // 失败次数递增
            $newFailCount = $redis->incr($failKey);
            if ($newFailCount === 1) $redis->expire($failKey, 3600);

            // 检查是否达到锁定阈值

            if ($newFailCount >= $maxAttempts) {
                $redis->expire($failKey, 3600);
                return $response->json([
                    'msg' => '账号或密码错误次数过多，账号已锁定，请60分钟后再试',
                    'refresh_captcha' => true
                ])->withStatus(401);
            }

            $remaining = $maxAttempts - $newFailCount;
            return $response->json([
                'msg' => "账号或密码错误，登录失败，剩余尝试次数：{$remaining}",
                'refresh_captcha' => true
            ])->withStatus(401);
        }

        // 6. 账号状态检查
        if ($model->status != 1) {
            return $response->json([
                'msg' => '账号已被禁用，登录失败',
                'refresh_captcha' => true
            ])->withStatus(403);
        }

        // 7. 登录成功，生成Token
        $accessToken = TokenUtil::createToken($model->id, $fingerprint, 3600 * 24 * 30);
        $refreshToken = TokenUtil::createToken($model->id, $fingerprint, 3600 * 24 * 7);

        $expiresTime = date('Y-m-d H:i:s', time() + 3600);
        $refreshExpiresTime = date('Y-m-d H:i:s', time() + 86400 * 7);

        UserToken::where('user_id', $model->id)->delete();
        UserToken::create([
            'user_id' => $model->id,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_time' => $expiresTime,
            'refresh_expires_time' => $refreshExpiresTime,
        ]);

        // 8. 清理所有限流标记
        $redis->del($failKey);

        return $response->json([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]);
    }
}