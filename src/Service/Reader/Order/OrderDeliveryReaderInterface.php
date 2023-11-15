<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Order;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

interface OrderDeliveryReaderInterface
{
    /**
     * @param string $orderId
     */
    public function getEntityByOrderId(string $orderId);

}