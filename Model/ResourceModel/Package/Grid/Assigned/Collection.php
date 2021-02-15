<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel\Package\Grid\Assigned;

use Netresearch\ShippingDispatch\Model\ResourceModel\Package\SearchResult;

class Collection extends SearchResult
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'nrshipping_dispatch_assigned_packages_grid_collection';
}
