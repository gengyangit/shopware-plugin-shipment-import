<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\StateMachine;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

class StateMachineStateReader implements StateMachineStateReaderInterface
{
    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository
     */
    protected EntityRepository $stateMachineStateRepository;

    /**
     * @var \Shopware\Core\Framework\Context
     */
    protected Context $context;

    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Framework\DataAbstractionLayer\EntityRepository $stateMachineStateRepository     
     */
    public function __construct(EntityRepository $stateMachineStateRepository) 
    {
        $this->stateMachineStateRepository = $stateMachineStateRepository;

        $this->context = Context::createDefaultContext();
    }

    /**
     * @var string $technicalName
     * 
     * @return 
     */
    public function getEntityByTechnicalName(string $technicalName)
    {
        $criteria = new Criteria();
        $criteria->addAssociation('stateMachine');
        $criteria->addFilter(new EqualsFilter('technicalName', $technicalName));
        
        $entities = $this
            ->stateMachineStateRepository
            ->search($criteria, $this->context)
            ->getEntities();

        if (count($entities) == 0) {
            return null;
        }
        
        return $entities->first();
    }

}