<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Webservice\DispatchResponse;

use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchSuccessResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\PackageErrorInterface;

class DispatchSuccessResponse implements DispatchSuccessResponseInterface
{
    /**
     * @var string
     */
    private $requestIndex;

    /**
     * @var string
     */
    private $dispatchNumber;

    /**
     * @var string
     */
    private $dispatchDate;

    /**
     * @var DocumentInterface[]
     */
    private $dispatchDocuments;

    /**
     * @return Phrase[]
     */
    private $dispatchErrors;

    /**
     * @var DispatchInterface
     */
    private $dispatch;

    /**
     * @var ShipmentTrackInterface[]
     */
    private $tracks;

    /**
     * DispatchSuccessResponse constructor.
     *
     * @param string $requestIndex
     * @param string $dispatchNumber
     * @param string $dispatchDate
     * @param DocumentInterface[] $dispatchDocuments
     * @param PackageErrorInterface[] $dispatchErrors
     * @param DispatchInterface $dispatch
     * @param ShipmentTrackInterface[] $tracks
     */
    public function __construct(
        string $requestIndex,
        string $dispatchNumber,
        string $dispatchDate,
        array $dispatchDocuments,
        array $dispatchErrors,
        DispatchInterface $dispatch,
        array $tracks
    ) {
        $this->requestIndex = $requestIndex;
        $this->dispatchNumber = $dispatchNumber;
        $this->dispatchDate = $dispatchDate;
        $this->dispatchDocuments = $dispatchDocuments;
        $this->dispatchErrors = $dispatchErrors;
        $this->dispatch = $dispatch;
        $this->tracks = $tracks;
    }

    public function getRequestIndex(): string
    {
        return (string) $this->requestIndex;
    }

    public function getDispatchNumber(): string
    {
        return (string) $this->dispatchNumber;
    }

    public function getDispatchDate(): string
    {
        return (string) $this->dispatchDate;
    }

    public function getDispatchDocuments(): array
    {
        return $this->dispatchDocuments;
    }

    public function getDispatchErrors(): array
    {
        return $this->dispatchErrors;
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
