<?php declare(strict_types=1);

namespace Yanduu\ShipmentImport\Core\Content\ShipmentQueue;

use \DateTimeInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ShipmentQueueEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected string $orderNumber = '';

    /**
     * @var string
     */
    protected string $externOrderNumber = '';

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $data;

    /**
     * @var string
     */
    protected string $status = '';

    /**
     * @var DateTimeInterface 
     */
    protected $createdAt;

    /**
     * @var DateTimeInterface 
     */
    protected $updatedAt;

    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     * 
     * @return void
     */
    public function setOrderNumber(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return string
     */
    public function getExternOrderNumber(): string
    {
        return $this->externOrderNumber;
    }

    /**
     * @param string $externOrderNumber
     * 
     * @return void
     */
    public function setExternOrderNumber(string $externOrderNumber): void
    {
        $this->externOrderNumber = $externOrderNumber;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     * 
     * @return void
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * 
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface 
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * 
     * @return void
     */
    public function setCreatedAt(DateTimeInterface  $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface 
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * 
     * @return void
     */
    public function setUpdatedAt(DateTimeInterface  $updatedAt): void
    {
        $this->updatedAt = $udpatedAt;
    }
}