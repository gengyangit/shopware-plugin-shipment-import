<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Manager;

use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;

interface OrderManagerInterface
{
    /**
     * @return void
     */
    public function updateState(ShipmentQueueEntity $shipmentEntity): void;
}