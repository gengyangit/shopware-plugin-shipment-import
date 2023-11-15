<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Logger;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler as MonologStreamHandler;

class Logger implements LoggerInterface
{
    protected const MONOLOGGER_CHANNEL_NAME = 'YanduuShipmentImportLogger';

    /**
     * @var \Monolog\Logger
     */
    protected $logger;

    public function __construct() 
    {           
        $this->initLogger();
    }

    /**
     * @return void
     */
    protected function initLogger(): void 
    {
        $path = '/var/www/vhosts/yarnstore.de/staging.yarnstore.de/var/log/yanduu_shipment_import-' . date('Y-m-d', time()). '.log';

        $this->logger = new MonologLogger(static::MONOLOGGER_CHANNEL_NAME);        
        $this->logger->pushHandler(new MonologStreamHandler($path, MonologLogger::DEBUG));
    }

    /**
     * @param string $message
     * 
     * @return void
     */
    public function addInfo(string $message): void 
    {
        $this->logger->info($message);
    }

    /**
     * @param message
     * 
     * @return void
     */
    public function addError(string $message): void 
    {
        $this->logger->error($message);
    }
}