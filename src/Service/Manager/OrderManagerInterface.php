<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Manager;

use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;

interface OrderManagerInterface
{
    /**
     * @param ShipmentQueueEntity $shipmentEntity
     * 
     * @return string
     */
    public function updateState(ShipmentQueueEntity $shipmentEntity): string;
}