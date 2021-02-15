<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Data provider for dispatch form.
 */
class Dispatch extends AbstractDataProvider
{
    /**
     * The form is only used for creating a new dispatch, not for editing. No data to populate.
     *
     * @return mixed[][]
     */
    public function getData(): array
    {
        return [];
    }

    public function addFilter(Filter $filter): self
    {
        return $this;
    }
}
