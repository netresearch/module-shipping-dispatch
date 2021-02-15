<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;

/**
 * @api
 */
interface CancellationResponseInterface
{
    /**
     * Obtain request index (sequence for request-response association).
     *
     * @return string
     */
    public function getRequestIndex(): string;

    /**
     * Obtain current dispatch model.
     *
     * @return DispatchInterface
     */
    public function getDispatch(): DispatchInterface;

    /**
     * Obtain tracks associated to the current dispatch.
     *
     * @return ShipmentTrackInterface[]
     */
    public function getTracks(): array;
}
