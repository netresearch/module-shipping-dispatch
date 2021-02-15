<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch as DispatchResource;

class Dispatch extends AbstractModel implements DispatchInterface
{
    protected function _construct()
    {
        $this->_init(DispatchResource::class);
        parent::_construct();
    }

    public function getEntityId(): int
    {
        return (int) $this->getId();
    }

    public function getWebsiteId(): int
    {
        return (int)$this->getData(self::WEBSITE_ID);
    }

    public function getCarrierCode(): string
    {
        return (string)$this->getData(self::CARRIER_CODE);
    }

    public function getCarrierName(): string
    {
        return (string)$this->getData(self::CARRIER_NAME);
    }

    public function getPackageQty(): int
    {
        return (int)$this->getData(self::PACKAGE_QTY);
    }

    public function getStatus(): string
    {
        return (string)$this->getData(self::STATUS);
    }

    public function getDispatchNumber(): string
    {
        return (string)$this->getData(self::DISPATCH_NUMBER);
    }

    public function getDispatchDate(): string
    {
        return (string)$this->getData(self::DISPATCH_DATE);
    }

    public function getDispatchDocuments(): array
    {
        return (array) $this->getData(self::DISPATCH_DOCUMENTS);
    }
}
