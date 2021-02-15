<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\BulkDispatch;

use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchResponseInterface;

interface DispatchManagementInterface
{
    /**
     * Manifest dispatch via carrier API.
     *
     * @param DispatchInterface[] $dispatches
     * @return DispatchResponseInterface[]
     */
    public function manifest(array $dispatches): array;

    /**
     * Cancel dispatches at the carrier API.
     *
     * @param DispatchInterface[] $dispatches
     * @return CancellationResponseInterface[]
     */
    public function cancel(array $dispatches): array;
}
