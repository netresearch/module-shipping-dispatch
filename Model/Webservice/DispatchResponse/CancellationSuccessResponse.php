<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Webservice\DispatchResponse;

use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationSuccessResponseInterface;

class CancellationSuccessResponse implements CancellationSuccessResponseInterface
{
    /**
     * @var string
     */
    private $requestIndex;

    /**
     * @var DispatchInterface
     */
    private $dispatch;

    /**
     * @var ShipmentTrackInterface[]
     */
    private $tracks;

    /**
     * CancellationSuccessResponse constructor.
     *
     * @param string $requestIndex
     * @param DispatchInterface $dispatch
     * @param ShipmentTrackInterface[] $tracks
     */
    public function __construct(
        string $requestIndex,
        DispatchInterface $dispatch,
        array $tracks
    ) {
        $this->requestIndex = $requestIndex;
        $this->dispatch = $dispatch;
        $this->tracks = $tracks;
    }

    public function getRequestIndex(): string
    {
        return (string) $this->requestIndex;
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
