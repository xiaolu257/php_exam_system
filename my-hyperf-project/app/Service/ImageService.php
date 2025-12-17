<?php

namespace App\Service;


use Exception;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;
use Random\RandomException;

class ImageService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('ImageService', 'upload_files');
    }


//    /**
//     * 获取头像或头像缩略图
//     * @param string $avatarUrl
//     * @param string $baseUrl
//     * @return File|Json
//     */
//    public static function getImageFromPath(string $avatarUrl, string $baseUrl): File|Json
//    {
//        try {
//            if (!$avatarUrl) {
//                return json(['error' => 'No data provided']);
//            }
//
//            $avatarPath = app()->getRootPath() . "uploads/$baseUrl/" . $avatarUrl;
//            if (!file_exists($avatarPath)) {
//                return json(['error' => '头像路径不存在']);
//            }
//
//            return download($avatarPath, "$avatarUrl.jpg", false, 1)->force(false);
//        } catch (Exception $e) {
//            return json(['error' => '服务器内部错误: ' . $e->getMessage()]);
//        }
//    }


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
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
        // 返回前端可访问的 URL 路径
        return $date . '/' . $avatarName;
    }

}
