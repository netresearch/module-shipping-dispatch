<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

/**
 * @api
 */
interface CancellationErrorResponseInterface extends CancellationResponseInterface
{
    /**
     * Obtain error message.
     *
     * @return string
     */
    public function getError(): string;
}
