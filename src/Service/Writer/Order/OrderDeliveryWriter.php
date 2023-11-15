<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Writer\Order;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class OrderDeliveryWriter implements OrderDeliveryWriterInterface
{
    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface
     */
    private $orderDeliveryRepository;

    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $orderDeliveryRepository     
     */
    public function __construct(
        EntityRepositoryInterface $orderDeliveryRepository,
    ) {
        $this->orderDeliveryRepository = $orderDeliveryRepository;

        $this->context = Context::createDefaultContext();
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function update(array $data): EntityWrittenContainerEvent 
    {
        return $this->orderDeliveryRepository->update([$data], $this->context);
    }

}