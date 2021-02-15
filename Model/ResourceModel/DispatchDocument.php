<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DispatchDocument extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('nrshipping_dispatch_document', 'entity_id');
    }
}
