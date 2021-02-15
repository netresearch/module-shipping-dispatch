<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Webservice\DispatchResponse;

use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\PackageErrorInterface;

class PackageError implements PackageErrorInterface
{
    /**
     * @var int
     */
    private $trackId;

    /**
     * @var string
     */
    private $errorMessage;

    public function __construct(int $trackId, string $errorMessage)
    {
        $this->trackId = $trackId;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return int
     */
    public function getTrackId(): int
    {
        return $this->trackId;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
