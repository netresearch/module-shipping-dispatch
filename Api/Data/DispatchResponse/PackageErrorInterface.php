<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

/**
 * For web service requests which succeeded partially, a package error represents one track that could not be processed.
 *
 * @api
 */
interface PackageErrorInterface
{
    public function getTrackId(): int;

    public function getErrorMessage(): string;
}
