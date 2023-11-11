<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Writer\Queue;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\Uuid\Uuid;

class ShipmentQueueWriter implements ShipmentQueueWriterInterface
{
    /**
     * @var string
     */
    protected const STATUS_PENDING = "pending";

    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $shipmentQueueRepository
     */
    protected $shipmentQueueRepository;

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
     * @param array<string, mixed> $data
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */
    public function create(array $data): EntityWrittenContainerEvent 
    {
        return $this->shipmentQueueRepository->create([
            [
                'id' =>  Uuid::randomHex(),
                'order_number' => $data['order_number'],
                'extern_order_number' => $data['extern_order_number'],
                'data' => $data['data'],
                'status' => static::STATUS_PENDING,
            ]
        ], $this->context);

    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent
     */

    public function update(array $data): EntityWrittenContainerEvent 
    {
        return $this->shipmentQueueRepository->update([$data], $this->context);
    }

}