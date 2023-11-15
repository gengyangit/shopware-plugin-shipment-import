<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Logger;

interface LoggerInterface
{
    /**
     * @param string $message
     * 
     * @return void
     */
    public function addInfo(string $message): void;

    /**
     * @param string $message
     * 
     * @return void
     */
    public function addError(string $message): void;
}