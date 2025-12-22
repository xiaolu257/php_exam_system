<?php

namespace App\Service;


use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;
use Random\RandomException;
use Throwable;

class ImageService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('ImageService', 'upload_files');
    }

    public function saveUserAvatar(UploadedFile $file, string $username): ?string
    {
        // 获取扩展名
        $ext = $file->getExtension();
        // 随机字符串
        try {
            $randString = bin2hex(random_bytes(4));
        } catch (RandomException $e) {
            $this->logger->warning('random_bytes failed, fallback. ' . $e->getMessage());
            $randString = time() . mt_rand(1000, 9999);
        }
        //加上随机字符串组成文件名
        $avatarName = $username . '_' . $randString . '.' . $ext;
        $avatarDir = 'uploads/userAvatars';
        $date = date('Y-m-d');

        // 真实保存目录（绝对路径）
        $saveDir = BASE_PATH . '/' . $avatarDir . '/' . $date;

        // 创建目录
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

        // 最终保存路径
        $savePath = $saveDir . '/' . $avatarName;

        // 移动文件
        try {
            $file->moveTo($savePath);
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
        // 返回前端可访问的 URL 路径
        return $date . '/' . $avatarName;
    }

    public function deleteUserAvatar(string $avatarUrl): bool
    {
        if (empty($avatarUrl) || in_array($avatarUrl, ['default.jpg', 'default.png'], true)) {
            return false;
        }

        // 真实文件路径
        $filePath = BASE_PATH . '/uploads/userAvatars/' . $avatarUrl;

        // 不存在直接返回 true（幂等）
        if (!file_exists($filePath)) {
            $this->logger->info("Avatar not found: {$filePath}");
            return true;
        }

        // 非文件（安全兜底）
        if (!is_file($filePath)) {
            $this->logger->warning("Avatar path is not file: {$filePath}");
            return false;
        }

        try {
            if (unlink($filePath)) {
                $dir = dirname($filePath);
                // 只删空目录
                if (is_dir($dir) && count(scandir($dir)) === 2) {
                    @rmdir($dir);
                }
                return true;
            }
            return false;
        } catch (Throwable $e) {
            $this->logger->error('Delete avatar failed: ' . $e->getMessage());
            return false;
        }
    }
}
