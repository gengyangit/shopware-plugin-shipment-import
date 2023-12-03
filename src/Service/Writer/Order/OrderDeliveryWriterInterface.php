<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Writer\Order;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

interface OrderDeliveryWriterInterface
{
    /**
     * @param array<string, mixed> $data
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function update(array $data): EntityWrittenContainerEvent;
}