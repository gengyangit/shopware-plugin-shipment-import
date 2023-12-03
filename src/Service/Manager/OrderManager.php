<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Manager;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryStates;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Mime\Email;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;
use Yanduu\ShipmentImport\Service\Config\SystemConfig;
use Yanduu\ShipmentImport\Service\Mail\MailService;
use Yanduu\ShipmentImport\Service\Reader\StateMachine\StateMachineStateReaderInterface;
use Yanduu\ShipmentImport\Service\Reader\Order\OrderReaderInterface;
use Yanduu\ShipmentImport\Service\Reader\Order\OrderDeliveryReaderInterface;
use Yanduu\ShipmentImport\Service\Writer\Order\OrderDeliveryWriterInterface;

class OrderManager implements OrderManagerInterface
{
    /**
     * @var string
     */
    protected const STATUS_PENDING = "pending";

    /**
     * @var Yanduu\ShipmentImport\Service\Config\SystemConfig
     */
    protected SystemConfig $config;

    /**
     * @var \Yanduu\ShipmentImport\Service\Mail\MailService
     */
    protected MailService $mailService;

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
     * @param \Yanduu\ShipmentImport\Service\Mail\MailService $mailService
     * @param \Yanduu\ShipmentImport\Service\Config\SystemConfig $config
     */
    public function __construct(
        OrderReaderInterface $orderReader,
        OrderDeliveryReaderInterface $orderDeliveryReader,
        StateMachineStateReaderInterface $stateMachineStateReader,
        OrderDeliveryWriterInterface $orderDeliveryWriter,
        MailService $mailService,
        SystemConfig $config
    ) {
        $this->config = $config;
        $this->mailService = $mailService;
        $this->orderReader = $orderReader;
        $this->orderDeliveryReader = $orderDeliveryReader;
        $this->stateMachineStateReader = $stateMachineStateReader;
        $this->orderDeliveryWriter = $orderDeliveryWriter;
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return string
     */
    public function updateState(ShipmentQueueEntity $shipmentEntity): string
    {   
        $shipmentData = $shipmentEntity->getData();
        $shipment = $this->getShipment($shipmentData['line_items']);
        $state = $this->stateMachineStateReader->getEntityByTechnicalName($shipment['state']);
        $order = $this->orderReader->getEntityByOrderNumber($shipmentEntity->getOrderNumber());
        $deliveries = $this->orderDeliveryReader->getEntitiesByOrderId($order->getId());

        /** @ToDo check if multiple Shipping */
        $delivery = $deliveries->first();
        $savedTrackingCodes = $delivery->getTrackingCodes();

        $orderDeliveryData= [ 
            'id' => $delivery->getId(), 
            'stateId' => $state->getId(),
            'trackingCodes' => array_unique(array_merge($savedTrackingCodes, $shipment['tracking_codes']))
        ];     

        $this->orderDeliveryWriter->update($orderDeliveryData);
        $delivery->setTrackingCodes($shipment['tracking_codes']);
        $orderDeliveryCollection = new OrderDeliveryCollection();
        $orderDeliveryCollection->add($delivery);
        $order->setDeliveries($orderDeliveryCollection);

        if ($this->checkSendEmail($savedTrackingCodes, $shipment['tracking_codes'])) {
            $this->sendEmail($order, array_merge($shipmentData, $shipment));
        }
        
        
        return $shipment['state'];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getShipment(array $lineItems): array 
    {
        $trackingCodes = [];
        $deliveryState = OrderDeliveryStates::STATE_OPEN;
        $quantityOrdered = 0;
        $quantityShipped = 0;

        foreach ($lineItems as $lineItem) {
            $quantityOrdered = $quantityOrdered + $lineItem['quantity_ordered'];

            if ($lineItem['quantity_shipped']) {
                $quantityShipped = $quantityShipped + $lineItem['quantity_shipped'];
            }

            if (!array_key_exists('shipments', $lineItem)
                || empty($lineItem['shipments'])
            ) {
                continue;
            }

            if (!in_array($lineItem['shipments']['tracking_code'], $trackingCodes)) {
                array_push($trackingCodes, $lineItem['shipments']['tracking_code']);
            }
        }

        if ($quantityShipped > 0
            && $quantityShipped < $quantityOrdered 
        ) {
            $deliveryState = OrderDeliveryStates::STATE_PARTIALLY_SHIPPED;
        }

        if ($quantityShipped > 0
            && $quantityShipped === $quantityOrdered 
        ) {
            $deliveryState = OrderDeliveryStates::STATE_SHIPPED;
        }

        return [
            'state' => $deliveryState,
            'tracking_codes' => $trackingCodes
        ];
    }

    /**
     * @param Shopware\Core\Checkout\Order\OrderEntity $order
     * @param array<string, mixed> $shipment
     * 
     * @return \Symfony\Component\Mime\Email|null
     */
    protected function sendEmail(OrderEntity $order, $shipment): ?Email 
    {
       
        $email = $shipment['customer']['email'];
        $name = $shipment['customer']['firstname']. ' ' . $shipment['customer']['lastname'];

        if (
            !array_key_exists('state', $shipment)
            || !isset($shipment['state'])
        ) {
            return null ;
        }

        $templateId = '';

        if ($shipment['state'] === OrderDeliveryStates::STATE_PARTIALLY_SHIPPED) {
            $templateId = $this->config->getPartiallyShippedEmailTemplateId();
        }

        if ($shipment['state'] === OrderDeliveryStates::STATE_SHIPPED) {
            $templateId = $this->config->getShippedEmailTemplateId();
        }

        return $this->mailService->send(
            [$email => $name], 
            $salesChannelContext,
            [
                'order' => $order,
                'template_id' => $templateId
            ]
        );
    }

    /**
     * @param array<int, string> $savedTrackingCodes
     * @param array<int, string> $newTrackingCodes
     * 
     * @return bool
     */
    protected function checkSendEmail(
        $savedTrackingCodes, 
        $newTrackingCodes
    ): bool {

        foreach ($newTrackingCodes as $trackingCode) {
            if (!in_array($trackingCode, $savedTrackingCodes)) {
                return true;
            }
        }

        return false;
    }
}