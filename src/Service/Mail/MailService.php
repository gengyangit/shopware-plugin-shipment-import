<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Service\Mail;

use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Mime\Email;

class MailService implements MailServiceInterface
{
    /**
     * @var \Shopware\Core\Content\Mail\Service\AbstractMailService
     */
    protected AbstractMailService $mailService;

    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface
     */
    protected EntityRepositoryInterface $mailTemplateRepository;

    /**
     * @var \Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface
     */
    protected EntityRepositoryInterface $salesChannelRepository;

    /**
     * @var \Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory
     */
    protected AbstractSalesChannelContextFactory $salesChannelContextFactory;
 
    /**
     * Constructor 
     * 
     * @param \Shopware\Core\Content\Mail\Service\AbstractMailService $mailService
     */
    public function __construct(
        AbstractMailService $mailService,
        AbstractSalesChannelContextFactory $salesChannelContextFactory,
        EntityRepositoryInterface $mailTemplateRepository,
        EntityRepositoryInterface $salesChannelRepository
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->salesChannelContextFactory = $salesChannelContextFactory;
    }

    /**
     * Method for sending an email notification
     *
     * @param array<int string> $recipients
     * @param SalesChannelContext $salesChannelContext
     * @param array<string, mixed> $templateData
     * 
     * @return \use Symfony\Component\Mime\Email|null
     */
    public function send(
        array $recipients,
        SalesChannelContext $salesChannelContext = null,
        array $templateData = []
    ): ?Email {

        if (
            !array_key_exists('template_id', $templateData)
            || !isset($templateData['template_id'])
        ) {
            return null;
        }

        $mailTemplate = $this->getMailTemplate($templateData['template_id']);

        if (!$mailTemplate) {
            return null;
        }

        $data = new DataBag();
        $data->set('recipients', $recipients); 
        $data->set('senderName', $mailTemplate->getSenderName());
        $data->set('subject', $mailTemplate->getTranslation('subject'));
        $data->set('contentHtml', $mailTemplate->getTranslation('contentHtml'));
        $data->set('contentPlain', $mailTemplate->getTranslation('contentPlain'));
        $data->set('templateId', $mailTemplate->getId());

        if (!$salesChannelContext) {
            $salesChannelContext = $this->getSalesChannelContext();
        }

        $data->set('salesChannelId', $salesChannelContext->getSalesChannel()->getId());
        
        return $this->mailService->send(
            $data->all(), 
            $salesChannelContext->getContext(), 
            $templateData
        );
    }


    /**
     * Method for creating a sales channel context
     *
     * @param string|null $salesChannelId
     * @param string|null $languageId
     * 
     * @return \Shopware\Core\System\SalesChannel\SalesChannelContext
     */
    protected function getSalesChannelContext(
        string $salesChannelId = null, 
        string $languageId = null
    ): SalesChannelContext {
        //get the sales channel ID and language ID, if they are missing
        if (!isset($salesChannelId) || !isset($languageId)) {
            $criteria = new Criteria();
            if (isset($salesChannelId)) {
                $criteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
            }
            if (isset($languageId)) {
                $criteria->addFilter(new EqualsFilter('languageId', $languageId));
            }
 
            /** @var SalesChannelEntity $salesChannel */
            $salesChannel = $this->salesChannelRepository->search($criteria, Context::createDefaultContext())->first();
            if ($salesChannel) {
                $salesChannelId = $salesChannel->getId();
                $languageId = $salesChannel->getLanguageId();
            }
        }
 
        return $this->salesChannelContextFactory->create('', $salesChannelId, [SalesChannelContextService::LANGUAGE_ID => $languageId]);
    }

    /**
     * Method for getting an email template by its ID or the first one available, if no ID is supplied
     *
     * @param string $id
     * @param Context $context
     * @return MailTemplateEntity|null
     */
    protected function getMailTemplate(
        string $id = null, 
        Context $context = null
    ): ?MailTemplateEntity {
        //get the sales channel context, if not already present
        if (!isset($context)) {
            $salesChannelContext = $this->getSalesChannelContext();
            $context = $salesChannelContext->getContext();
        }
 
        //set the criteria for searching in the mail template repository
        $criteria = new Criteria();
        $criteria->addAssociation('media.media');
        $criteria->setLimit(1);
 
        //if a template ID was passed, we will get that template, otherwise just the first one the repository returns
        if (isset($id)) {
            $criteria->addFilter(new EqualsFilter('id', $id));
        }
 
        //get and return one template
        return $this->mailTemplateRepository->search($criteria, $context)->first();
    }

}