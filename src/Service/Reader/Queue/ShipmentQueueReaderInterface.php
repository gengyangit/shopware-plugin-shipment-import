<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Queue;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;

interface ShipmentQueueReaderInterface
{
    /**
     * @param Criteria $criteria
     * 
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection 
     */
    public function getCollection(Criteria $criteria): ShipmentQueueCollection;

    /**
     * @param string $orderNumber
     */
    public function getEntityByOrderNumber(string $productNumber);

}