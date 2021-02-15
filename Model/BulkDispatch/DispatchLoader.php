<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\BulkDispatch;

use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch\CollectionFactory;

/**
 * Load dispatches.
 */
class DispatchLoader
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Load dispatches, optionally filtered by status.
     *
     * @param string $status
     * @return DispatchInterface[]
     */
    public function getDispatches(string $status = null): array
    {
        $collection = $this->collectionFactory->create();

        if ($status) {
            $collection->addFieldToFilter(DispatchInterface::STATUS, ['eq' => $status]);
        }

        return $collection->getItems();
    }

    public function getPendingDispatch(string $carrierCode, int $websiteId): ?DispatchInterface
    {
        $collection = $this->collectionFactory->create();
        $collection
            ->addFieldToFilter(DispatchInterface::CARRIER_CODE, ['eq' => $carrierCode])
            ->addFieldToFilter(DispatchInterface::WEBSITE_ID, ['eq' => $websiteId])
            ->addFieldToFilter(DispatchInterface::STATUS, ['eq' => DispatchInterface::STATUS_PENDING])
            ->addOrder('entity_id')
            ->setPageSize(1)
            ->setCurPage(1);

        $dispatch = $collection->fetchItem();
        if (!$dispatch instanceof DispatchInterface) {
            return null;
        }

        return $dispatch;
    }
}
