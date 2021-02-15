<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data\DispatchResponse;

/**
 * Dispatch response with manifestation documents. May still contain errors for some of the requested packages.
 *
 * @api
 */
interface DispatchSuccessResponseInterface extends DispatchResponseInterface
{
    /**
     * Obtain dispatch number (identifier created at the API).
     *
     * @return string
     */
    public function getDispatchNumber(): string;

    /**
     * Obtain dispatch date (date of dispatch creation).
     *
     * @return string
     */
    public function getDispatchDate(): string;

    /**
     * Obtain dispatch documents (any documents created at the api).
     *
     * @return DocumentInterface[]
     */
    public function getDispatchDocuments(): array;

    /**
     * Obtain manifestation errors.
     *
     * @return PackageErrorInterface[]
     */
    public function getDispatchErrors(): array;
}
