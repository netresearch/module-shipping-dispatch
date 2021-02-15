<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Api\BulkDispatch;

interface ConfigurationInterface
{
    public function getCarrierCode(): string;

    public function getDispatchManagement(): DispatchManagementInterface;
}
