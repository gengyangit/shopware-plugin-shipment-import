<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Core\Content\ShipmentQueue;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;

class ShipmentQueueDefinition extends EntityDefinition
{
    /**
     * @var string
     */
    public const ENTITY_NAME = 'yanduu_shipment_queue';

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return Shopware\Core\Framework\DataAbstractionLayer\FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('order_number', 'orderNumber')),
            (new StringField('extern_order_number', 'externOrderNumber')),
            (new JsonField('data', 'data')),
            (new StringField('status', 'status')),
            (new DateField('created_at', 'createdAt')),
            (new DateField('updated_at', 'updatedAt'))
        ]);
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return ShipmentQueueEntity::class;
    }

    /**
     * @var string
     */
    public function getCollectionClass(): string
    {
        return ShipmentQueueCollection::class;
    }

}