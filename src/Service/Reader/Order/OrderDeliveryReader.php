<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Order;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;


class OrderDeliveryReader implements OrderDeliveryReaderInterface
{
    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository
     */
    protected EntityRepository $orderDeliveryRepository;

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected Context $context;

    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $orderDeliveryRepository     
     */
    public function __construct(EntityRepository $orderDeliveryRepository) 
    {
        $this->orderDeliveryRepository = $orderDeliveryRepository;

        $this->context = Context::createDefaultContext();
    }

    /**
     * @var string $orderId
     * 
     * @return 
     */
    public function getEntitiesByOrderId(string $orderId) 
    {
        $collection = [];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderId', $orderId));
        
        $entities = $this
            ->orderDeliveryRepository
            ->search($criteria, $this->context)
            ->getEntities();

        if (count($entities) == 0) {
            return null;
        }
        
        return $entities;
    }

}