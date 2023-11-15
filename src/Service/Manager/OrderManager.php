<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Manager;

use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryStates;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;
use Yanduu\ShipmentImport\Service\Reader\StateMachine\StateMachineStateReaderInterface;
use Yanduu\ShipmentImport\Service\Reader\Order\OrderReaderInterface;
use Yanduu\ShipmentImport\Service\Reader\Order\OrderDeliveryReaderInterface;
use Yanduu\ShipmentImport\Service\Writer\Order\OrderDeliveryWriterInterface;

class OrderManager implements OrderManagerInterface
{
    protected const STATUS_PENDING = "pending";

    /**
     * @var \Yanduu\ShipmentImport\Service\Reader\Order\OrderReaderInterface
     */
    protected OrderReaderInterface $orderReader;

    /**
     * @var \Yanduu\ShipmentImport\Service\Reader\Order\OrderDeliveryReaderInterface
     */
    protected OrderDeliveryReaderInterface $orderDeliveryReader;

    /**
     * @var \Yanduu\ShipmentImport\Service\Reader\StateMachine\StateMachineStateReaderInterface
     */
    protected StateMachineStateReaderInterface $stateMachineStateReader;

    /**
     * @var \Yanduu\ShipmentImport\Service\Writer\Order\OrderDeliveryWriterInterface
     */
    protected OrderDeliveryWriterInterface $orderDeliveryWriter;

    /**
     * Constructor 
     * 
     * @param \Yanduu\ShipmentImport\Service\Reader\Order\OrderReaderInterface $orderReader
     * @param \Yanduu\ShipmentImport\Service\Reader\Order\OrderDeliveryReaderInterface $orderDeliveryReader
     * @param \Yanduu\ShipmentImport\Service\Reader\StateMachine\StateMachineReaderInterface $stateMachineStateReader
     * @param \Yanduu\ShipmentImport\Service\Writer\Order\OrderDeliveryWriterInterface $orderDeliveryWriter
     */
    public function __construct(
        OrderReaderInterface $orderReader,
        OrderDeliveryReaderInterface $orderDeliveryReader,
        StateMachineStateReaderInterface $stateMachineStateReader,
        OrderDeliveryWriterInterface $orderDeliveryWriter,
    ) {
        $this->orderReader = $orderReader;
        $this->orderDeliveryReader = $orderDeliveryReader;
        $this->stateMachineStateReader = $stateMachineStateReader;
        $this->orderDeliveryWriter = $orderDeliveryWriter;
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return void
     */
    public function updateState(ShipmentQueueEntity $shipmentEntity): void
    {   
        $data = $shipmentEntity->getData();
        $shipment = $this->getShipment($data['line_items']);
        $state = $this->stateMachineStateReader->getEntityByTechnicalName($shipment['state']);
        $order = $this->orderReader->getEntityByOrderNUmber($shipmentEntity->getOrderNumber());
        $orderDelivery = $this->orderDeliveryReader->getEntityByOrderId($order->getId());

        $orderDeliveryData = [ 
            'id' => $orderDelivery->getId(), 
            'stateId' => $state->getId(),
            'trackingCodes' => $shipment['tracking_codes']
        ];

        $this->orderDeliveryWriter->update($orderDeliveryData);
    }

    /**
     * @return
     */
    protected function getShipment(array $lineItems): array 
    {
        $trackingCodes = [];
        $deliveryState = OrderDeliveryStates::STATE_OPEN;

        foreach ($lineItems as $lineItem) {

            if ($lineItem['quantity_shipped'] === 0) {
                continue;
            }

            if ($lineItem['quantity_shipped'] !== $lineItem['quantity_ordered']) {
                $deliveryState = OrderDeliveryStates::STATE_PARTIALLY_SHIPPED;
            }

            if ($lineItem['quantity_shipped'] === $lineItem['quantity_ordered']
                && $deliveryState === OrderDeliveryStates::STATE_OPEN
            ) {
                $deliveryState = OrderDeliveryStates::STATE_SHIPPED;
            }

            if (!array_key_exists('shipments', $lineItem)
                || !isset($lineItem['shipments'])
            ) {
                continue;
            }

            if (!in_array($lineItem['shipments']['tracking_number'], $trackingCodes)) {
                array_push($trackingCodes, $lineItem['shipments']['tracking_number']);
            }
        }

        return [
            'state' => $deliveryState,
            'tracking_codes' => $trackingCodes
        ];
    }
}