<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingDispatch\Model\DispatchDocument;
use Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument as DispatchDocumentResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(DispatchDocument::class, DispatchDocumentResource::class);
    }
}
