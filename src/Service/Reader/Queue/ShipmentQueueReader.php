<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Queue;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;

class ShipmentQueueReader implements ShipmentQueueReaderInterface
{
    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository
     */
    protected EntityRepository $shipmentQueueRepository;

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected Context $context;

    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $shipmentQueueRepository     
     */
    public function __construct(EntityRepository $shipmentQueueRepository) 
    {
        $this->shipmentQueueRepository = $shipmentQueueRepository;

        $this->context = Context::createDefaultContext();
    }

    /**
     * @param \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria $criteria
     * 
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection 
     */
    public function getCollection(Criteria $criteria): ShipmentQueueCollection 
    {
        return $this->shipmentQueueRepository
            ->search($criteria, $this->context)
            ->getEntities();
    }

    /**
     * @var string $orderNumber
     * 
     * @return 
     */
    public function getEntityByOrderNumber(string $orderNumber) 
    {
        $collection = [];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderNumber', $orderNumber));
        
        $entities = $this
            ->shipmentQueueRepository
            ->search($criteria, $this->context)
            ->getEntities();

        if (count($entities) == 0) {
            return null;
        }
        
        return $entities->first();
    }

}