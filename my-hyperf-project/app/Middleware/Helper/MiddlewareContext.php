<?php

namespace App\Middleware\Helper;

use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareContext
{
    #[Inject]
    protected ServerRequestInterface $request;

    public function setUserId(int $userId): void
    {
        $this->request = $this->request->withAttribute('userId', $userId);
    }

    public function getUserId(): int
    {
        return $this->request->getAttribute('userId');
    }

    public function setPermissions(array $permissions): void
    {
        $this->request = $this->request->withAttribute('permissions', $permissions);
    }

    public function getPermissions(): array
    {
        return $this->request->getAttribute('permissions');
    }
}