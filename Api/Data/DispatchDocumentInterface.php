<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data;

/**
 * @api
 */
interface DispatchDocumentInterface
{
    public const DISPATCH_ID = 'dispatch_id';
    public const NAME = 'name';
    public const CONTENT = 'content';
    public const FORMAT = 'format';

    /**
     * Obtain document identifier.
     *
     * @return int
     */
    public function getEntityId(): int;

    /**
     * Obtain dispatch id associated with document.
     *
     * @return int
     */
    public function getDispatchId(): int;

    /**
     * Obtain document name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Obtain document content.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Obtain document format.
     *
     * @return string
     */
    public function getFormat(): string;
}
