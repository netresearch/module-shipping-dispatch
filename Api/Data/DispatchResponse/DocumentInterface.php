<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

/**
 * @api
 */
interface DocumentInterface
{
    /**
     * Obtain name (human readable name for the type of document).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Obtain document format (e.g. PDF, PNG, …).
     *
     * @return string
     */
    public function getFormat(): string;

    /**
     * Obtain document content (decoded, binary document data).
     *
     * @return string
     */
    public function getContent(): string;
}
