<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Config;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class SystemConfig implements SystemConfigInterface
{
    /**
     * @var string
     */
    protected const SYSTEM_CONFIG_PARTIALLY_SHIPPED_EMAIL_TEMPLATE_ID = 'YanduuShipmentImport.config.partiallyShippedEmailTemplateId';

    /**
     * @var string
     */
    protected const SYSTEM_CONFIG_SHIPPED_EMAIL_TEMPLATE_ID = 'YanduuShipmentImport.config.shippedEmailTemplateId';

    /**
     * @var \Shopware\Core\System\SystemConfig\SystemConfigService
     */
    protected SystemConfigService $config;

    /**
     * @param \Shopware\Core\System\SystemConfig\SystemConfigService $config
     */
    public function __construct(SystemConfigService $config) 
    {
        $this->config = $config;
    }

    /**
     * @param string|null $salesChannelId
     * 
     * @return string
     * 
     */
    public function getPartiallyShippedEmailTemplateId(?string $salesChannelId = null): string
    {
        return $this->config->get(static::SYSTEM_CONFIG_PARTIALLY_SHIPPED_EMAIL_TEMPLATE_ID, $salesChannelId);
    }

     /**
     * @param string|null $salesChannelId
     * 
     * @return string
     * 
     */
    public function getShippedEmailTemplateId(?string $salesChannelId = null): string
    {
        return $this->config->get(static::SYSTEM_CONFIG_SHIPPED_EMAIL_TEMPLATE_ID, $salesChannelId);
    }

}