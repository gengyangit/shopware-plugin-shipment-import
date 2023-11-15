<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;

class ShipmentQueueSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntityWriteEvent::class => 'beforeWrite',
        ];
    }

    /**
     * @param \Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent $event
     * 
     * @return void
     */
    public function beforeWrite(EntityWriteEvent $event): void 
    {
        //@toDo to be implemented
    }
}