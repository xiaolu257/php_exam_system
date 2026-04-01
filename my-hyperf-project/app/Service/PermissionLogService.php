<?php

namespace App\Service;

use App\Model\PermissionLog;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class PermissionLogService
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('PermissionLogService', 'permission_logs');
    }

    public function record(string $action, string $name, array $old = null, array $new = null, ?string $batchId = null): void
    {
        // 1️⃣ 数据库日志
        PermissionLog::create([
            'batch_id' => $batchId,
            'action' => $action,
            'permission_name' => $name,
            'old_data' => $old,
            'new_data' => $new,
        ]);

        // 2️⃣ 文件日志
        $this->logger->info('permission_change', [
            'batch_id' => $batchId,
            'action' => $action,
            'name' => $name,
            'old' => $old,
            'new' => $new,
        ]);
    }
}