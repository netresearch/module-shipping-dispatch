<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model;

use Magento\Framework\Model\AbstractModel;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument as DocumentResource;

class DispatchDocument extends AbstractModel implements DispatchDocumentInterface
{
    protected function _construct()
    {
        $this->_init(DocumentResource::class);
        parent::_construct();
    }

    public function getEntityId(): int
    {
        return (int) $this->getId();
    }

    public function getDispatchId(): int
    {
        return (int) $this->getData(self::DISPATCH_ID);
    }

    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    public function getFormat(): string
    {
        return (string) $this->getData(self::FORMAT);
    }

    public function getContent(): string
    {
        return (string) $this->getData(self::CONTENT);
    }
}
