<?php

declare(strict_types=1);

namespace App\Controller\NormalUser;

use App\Annotation\Permission;
use App\Middleware\Helper\MiddlewareContext;
use App\Service\ImageService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

#[Controller(prefix: 'test')]
class NormalUserController
{
    #[Inject]
    protected ImageService $imageService;
    #[Inject]
    protected MiddlewareContext $authContext;

    #[GetMapping('index1')]
    #[Permission('e11', '测试1')]
    public function index1(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        var_dump($this->authContext->getUserId());
        return $response->raw('Hello Hyperf!');
    }

    #[GetMapping('index2')]
    #[Permission('e123', '测试12')]
    public function index2(RequestInterface $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        var_dump($this->authContext->getUserId());
        return $response->raw('Hello Hyperf!');
    }
}
