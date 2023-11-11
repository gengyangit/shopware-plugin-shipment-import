<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Writer\Queue;

use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;

interface ShipmentQueueWriterInterface
{
    /**
     * @param array<string, mixed> $data
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function create(array $data): EntityWrittenContainerEvent;

    /**
     * @param array<string, mixed>
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function update(array $data): EntityWrittenContainerEvent;

}