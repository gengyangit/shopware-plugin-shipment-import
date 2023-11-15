<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Command;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection;
use Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity;
use Yanduu\ShipmentImport\Service\Manager\OrderManagerInterface;
use Yanduu\ShipmentImport\Service\Reader\Queue\ShipmentQueueReaderInterface;

class ProcessShipmentsCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'yanduu-shipment-import:process-shipments';

    /**
     * @var string
     */
    protected const STATUS_PENDING = 'pending';

    /**
     * @var string
     */
    protected const STATUS_PROCESSING = 'processing';

    /**
     * @var string
     */
    protected const STATUS_PROCESSED = 'processed';

    /**
     * @var string
     */
    protected const SUCCESS_STATUS_CODE = 200;

    /**
     * @var \Yanduu\ShipmentImport\Service\Manager\OrderManagerInterface
     */
    protected $orderManager;

    /**
     * @var \Yanduu\ShipmentImport\Service\Reader\Queue\ShipmentQueueReaderInterface
     */
    protected $shipmentQueueReader;


    /**
     * Constructor 
     * 
     * @param \Yanduu\ShipmentImport\Service\Manager\OrderManagerInterface $orderManager
     * @param \Yarnstore\OrderExport\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        OrderManagerInterface $orderManager,
        ShipmentQueueReaderInterface $shipmentQueueReader,
        //LoggerInterface $logger,
    ) {
        $this->orderManager = $orderManager;
        $this->shipmentQueueReader = $shipmentQueueReader;

        parent::__construct();
    }

     /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Process Shipments');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Input\InputInterface $output
     * 
     * @return int
     */
    protected function execute(
        InputInterface $input, 
        OutputInterface $output
    ): int {
        $output->writeln('Start execute command!');

        $shipmentCollection = $this->getShipments();

        if (count($shipmentCollection) == 0) {
            return static::SUCCESS_STATUS_CODE;    
        }
        
        /** @var \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueEntity $shipmentEntity */
        foreach ($shipmentCollection as $shipmentEntity) {
            $this->updateShipmentState($shipmentEntity);
        }

        return static::SUCCESS_STATUS_CODE;
    }   
    
    /**
     * @return \Yanduu\ShipmentImport\Core\Content\ShipmentQueue\ShipmentQueueCollection
     */
    protected function getShipments(): ShipmentQueueCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('status', [static::STATUS_PENDING, static::STATUS_PROCESSING]));

        return $this->shipmentQueueReader->getCollection($criteria);       
    }

    /**
     * 
     */
    protected function updateShipmentState(ShipmentQueueEntity $shipmentEntity) 
    {
        $data = $shipmentEntity->getData();

        if (!$data) {
            return;
        }

        if (!array_key_exists('line_items', $data)
            || !isset($data['line_items'])
        ) {
            return;
        }


        $this->orderManager->updateState($shipmentEntity);
    }
    
}