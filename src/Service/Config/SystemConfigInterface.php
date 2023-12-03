<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Config;

interface SystemConfigInterface
{
    /**
     * @param string|null $salesChannelId
     * 
     * @return string
     */
    public function getPartiallyShippedEmailTemplateId(?string $salesChannelId = null): string;

    /**
     * @param string|null $salesChannelId
     * 
     * @return string
     */
    public function getShippedEmailTemplateId(?string $salesChannelId = null): string;
}