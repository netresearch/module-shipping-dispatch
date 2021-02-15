<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\ViewModel;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\DispatchRegistry;

class DispatchView implements ArgumentInterface
{
    /**
     * @var DispatchRegistry
     */
    private $dispatchRegistry;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        DispatchRegistry $dispatchRegistry,
        TimezoneInterface $localeDate,
        UrlInterface $urlBuilder
    ) {
        $this->dispatchRegistry = $dispatchRegistry;
        $this->localeDate = $localeDate;
        $this->urlBuilder = $urlBuilder;
    }

    public function getDispatchNumber(): string
    {
        $dispatch = $this->dispatchRegistry->getDispatch();
        return $dispatch ? $dispatch->getDispatchNumber() : '';
    }

    public function getDispatchDate(): string
    {
        $dispatch = $this->dispatchRegistry->getDispatch();
        if (!$dispatch) {
            return '';
        }

        return $this->localeDate->formatDateTime($dispatch->getDispatchDate(), \IntlDateFormatter::MEDIUM, true);
    }

    public function getDispatchStatus(): string
    {
        $dispatch = $this->dispatchRegistry->getDispatch();
        if (!$dispatch) {
            return '';
        }

        $status = $dispatch->getStatus();
        switch ($status) {
            case DispatchInterface::STATUS_PROCESSING:
                return __('Processing')->render();
            case DispatchInterface::STATUS_COMPLETE:
                return __('Complete')->render();
        }

        return ucfirst($status);
    }

    /**
     * @return DispatchDocumentInterface[]
     */
    public function getDispatchDocuments(): array
    {
        $dispatch = $this->dispatchRegistry->getDispatch();
        return $dispatch ? $dispatch->getDispatchDocuments() : [];
    }

    public function getDownloadLink(DispatchDocumentInterface $document): string
    {
        return $this->urlBuilder->getUrl(
            'nrshipping/dispatch_document/download',
            ['document_id' => $document->getEntityId()]
        );
    }
}
