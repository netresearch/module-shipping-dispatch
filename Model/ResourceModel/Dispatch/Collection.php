<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch as DispatchResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Dispatch::class, DispatchResource::class);
    }
}
