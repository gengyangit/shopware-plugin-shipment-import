<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\Order;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;


class OrderReader implements OrderReaderInterface
{
    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository
     */
    protected EntityRepository $orderRepository;

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected Context $context;

    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $orderRepository     
     */
    public function __construct(EntityRepository $orderRepository) 
    {
        $this->orderRepository = $orderRepository;

        $this->context = Context::createDefaultContext();
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
            ->orderRepository
            ->search($criteria, $this->context)
            ->getEntities();

        if (count($entities) == 0) {
            return null;
        }
        
        return $entities->first();
    }

}