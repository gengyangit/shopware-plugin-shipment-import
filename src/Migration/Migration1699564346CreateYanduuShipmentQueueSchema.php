<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1699564346CreateYanduuShipmentQueueSchema extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1699564346;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `yanduu_shipment_queue` (
                `id`  BINARY(16) NOT NULL,
                `order_number` VARCHAR(255) NOT NULL,
                `extern_order_number` VARCHAR(255) NOT NULL,
                `data` TEXT NOT NULL,
                `status` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
