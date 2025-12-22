<?php

declare(strict_types=1);

namespace App\Util;

use Psr\Http\Message\ResponseInterface;
use function Hyperf\Config\config;

class CorsUtil
{
    public static function apply(
        ResponseInterface $response,
        ?string           $origin
    ): ResponseInterface
    {
        $allowOrigins = config('cors.allow_origins', []);

        if ($origin && in_array($origin, $allowOrigins, true)) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader(
                    'Access-Control-Allow-Headers',
                    implode(',', config('cors.allow_headers', []))
                )
                ->withHeader(
                    'Access-Control-Allow-Methods',
                    implode(',', config('cors.allow_methods', []))
                );

            if (config('cors.allow_credentials', false)) {
                $response = $response->withHeader(
                    'Access-Control-Allow-Credentials',
                    'true'
                );
            }
        }

        return $response;
    }
}
