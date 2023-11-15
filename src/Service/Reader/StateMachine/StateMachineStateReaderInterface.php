<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Reader\StateMachine;

interface StateMachineStateReaderInterface
{

    /**
     * @param string $technicalName
     */
    public function getEntityByTechnicalName(string $technicalName);

}