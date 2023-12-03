<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Mail;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Mime\Email;

interface MailServiceInterface
{
    /**
     * Method for sending an email notification
     *
     * @param array<int string> $recipients
     * @param SalesChannelContext $salesChannelContext
     * @param array<string, mixed> $templateData
     * 
     * @return \Symfony\Component\Mime\Email|null
     */
    public function send(
        array $recipients,
        SalesChannelContext $salesChannelContext = null,
        array $templateData = []
    ): ?Email;

}