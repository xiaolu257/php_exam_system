<?php

namespace App\Service;


use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Logger\LoggerFactory;
use Imagick;
use Psr\Log\LoggerInterface;
use Throwable;

class ImageService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('ImageService', 'upload_files');
    }

    private function generateAvatarThumb(string $srcPath, string $dstPath, int $maxWidth = 150, int $maxHeight = 150): bool
    {
        try {
            $img = new Imagick($srcPath);

            // 等比缩放（不会裁剪）
            $img->thumbnailImage($maxWidth, $maxHeight, true);

            // 写入文件
            $img->writeImage($dstPath);

            // 释放资源
            $img->clear();

            return true;
        } catch (Throwable $e) {
            $this->logger->error('Generate avatar thumb failed: ' . $e->getMessage());
            return false;
        }
    }

    public function saveUserAvatar(UploadedFile $file, string $username): ?string
    {
        $ext = strtolower($file->getExtension());

        // 允许的图片类型
        $allowExt = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowExt, true)) {
            $this->logger->warning('Invalid avatar extension: ' . $ext);
            return null;
        }

        // 随机名
        try {
            $randString = bin2hex(random_bytes(4));
        } catch (Throwable) {
            $randString = time() . mt_rand(1000, 9999);
        }

        $avatarName = "{$username}_{$randString}.{$ext}";
        $date = date('Y-m-d');

        $baseDir = BASE_PATH . '/uploads/userAvatars';
        $originDir = "{$baseDir}/original/{$date}";
        $thumbDir = "{$baseDir}/thumb/{$date}";

        foreach ([$originDir, $thumbDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $originPath = "{$originDir}/{$avatarName}";
        $thumbPath = "{$thumbDir}/{$avatarName}";

        // 保存原图
        try {
            $file->moveTo($originPath);
            // 生成缩略图（默认 150*150）
            if (!$this->generateAvatarThumb($originPath, $thumbPath)) {
                @unlink($originPath);
                @unlink($thumbPath);
                return null;
            }
        } catch (Throwable $e) {
            $this->logger->error('Save original avatar failed: ' . $e->getMessage());
            return null;
        }

        // 返回逻辑路径（只存一份）
        return "{$date}/{$avatarName}";
    }


    public function deleteUserAvatar(string $avatarUrl): void
    {
        if (empty($avatarUrl) || in_array($avatarUrl, ['default.jpg', 'default.png'], true)) {
            return;
        }

        $baseDir = BASE_PATH . '/uploads/userAvatars';

        $paths = [
            "$baseDir/original/$avatarUrl",
            "$baseDir/thumb/$avatarUrl",
        ];

        //尽最大努力删除，前一个失败，后一个继续
        foreach ($paths as $path) {
            if (!file_exists($path)) {
                $this->logger->info("Avatar not found: $path");
                continue; // 幂等
            }

            if (!is_file($path)) {
                $this->logger->warning("Avatar path is not file: $path");
                continue;
            }

            try {
                if (!unlink($path)) {
                    $this->logger->warning("Failed to delete avatar: $path");
                    continue;
                }
                // 删除空目录（只删日期层）
                $dir = dirname($path);
                if (is_dir($dir) && count(scandir($dir)) === 2) {
                    @rmdir($dir);
                }
            } catch (Throwable $e) {
                $this->logger->error('Delete avatar failed: ' . $e->getMessage());
            }
        }
    }
}
