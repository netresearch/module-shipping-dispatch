<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Webservice\DispatchResponse;

use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationErrorResponseInterface;

class CancellationErrorResponse implements CancellationErrorResponseInterface
{
    /**
     * @var string
     */
    private $requestIndex;

    /**
     * @var string
     */
    private $error;

    /**
     * @var DispatchInterface
     */
    private $dispatch;

    /**
     * @var ShipmentTrackInterface[]
     */
    private $tracks;

    /**
     * CancellationErrorResponse constructor.
     *
     * @param string $requestIndex
     * @param string $error
     * @param DispatchInterface $dispatch
     * @param ShipmentTrackInterface[] $tracks
     */
    public function __construct(
        string $requestIndex,
        string $error,
        DispatchInterface $dispatch,
        array $tracks
    ) {
        $this->requestIndex = $requestIndex;
        $this->error = $error;
        $this->dispatch = $dispatch;
        $this->tracks = $tracks;
    }

    public function getRequestIndex(): string
    {
        return (string) $this->requestIndex;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getDispatch(): DispatchInterface
    {
        return $this->dispatch;
    }

    public function getTracks(): array
    {
        return $this->tracks;
    }
}
