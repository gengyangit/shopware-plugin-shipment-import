<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Order;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

interface OrderReaderInterface
{
    /**
     * @param string $orderNumber
     */
    public function getEntityByOrderNumber(string $orderNumber);

}