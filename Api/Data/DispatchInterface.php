<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\Data;

/**
 * @api
 */
interface DispatchInterface
{
    public const WEBSITE_ID = 'website_id';
    public const CARRIER_CODE = 'carrier_code';
    public const CARRIER_NAME = 'carrier_name';
    public const PACKAGE_QTY = 'package_qty';
    public const STATUS = 'status';
    public const DISPATCH_NUMBER = 'dispatch_number';
    public const DISPATCH_DATE = 'dispatch_date';
    public const DISPATCH_DOCUMENTS = 'dispatch_documents';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETE = 'complete';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELLED = 'cancelled';

    public function getEntityId(): int;

    public function getWebsiteId(): int;

    public function getCarrierCode(): string;

    public function getCarrierName(): string;

    public function getPackageQty(): int;

    /**
     * Obtain dispatch status.
     *
     * - Pending: Created locally, not sent to carrier API.
     * - Failed: Created locally, failed to be sent to carrier API.
     * - Processing: Sent to carrier API, manifestation documents not yet downloaded.
     * - Complete: Sent to carrier API, manifestation documents downloaded.
     * - Cancelled: Cancelled/deleted.
     *
     * @return string
     */
    public function getStatus(): string;

    public function getDispatchNumber(): string;

    public function getDispatchDate(): string;

    /**
     * @return DispatchDocumentInterface[]
     */
    public function getDispatchDocuments(): array;
}
