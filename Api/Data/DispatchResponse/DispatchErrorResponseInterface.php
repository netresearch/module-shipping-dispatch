<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

/**
 * Dispatch response with no manifestation documents. Request failed entirely.
 *
 * @api
 */
interface DispatchErrorResponseInterface extends DispatchResponseInterface
{
    /**
     * Obtain error message.
     *
     * @return string
     */
    public function getError(): string;
}
