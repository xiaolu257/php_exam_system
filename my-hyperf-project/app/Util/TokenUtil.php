<?php

namespace App\Util;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use function Hyperf\Support\env;

class TokenUtil
{
    private static function getJWTKey()
    {
        return env('JWT_KEY', 'dev_xiaolu');
    }

    public static function createToken(int $adminId, string $fingerprint, int $ttl = 3600): string
    {
        $payload = [
            'iss' => 'php_exam_system',
            'uid' => $adminId,
            'fingerprint' => password_hash($fingerprint, PASSWORD_DEFAULT),
            'iat' => time(),
            'exp' => time() + $ttl,
        ];
        $key = self::getJWTKey();
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function decodeToken(string $token): \stdClass
    {
        $key = self::getJWTKey();
        return JWT::decode($token, new Key($key, 'HS256'));
    }
}