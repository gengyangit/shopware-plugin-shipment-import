<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Core\Content\ShipmentQueue;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(ShipmentQueueEntity $entity)
 * @method void               set(string $key, ShipmentQueueEntity $entity)
 * @method ShipmentQueueEntity[]    getIterator()
 * @method ShipmentQueueEntity[]    getElements()
 * @method ShipmentQueueEntity|null get(string $key)
 * @method ShipmentQueueEntity|null first()
 * @method ShipmentQueueEntity|null last()
 */
class ShipmentQueueCollection extends EntityCollection
{
    /**
     * @return string
     */
    protected function getExpectedClass(): string
    {
        return ShipmentQueueEntity::class;
    }
}