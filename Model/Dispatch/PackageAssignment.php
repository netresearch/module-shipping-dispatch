<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Dispatch;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch;
use Netresearch\ShippingDispatch\Model\ResourceModel\Package\CollectionFactory;

class PackageAssignment
{
    /**
     * @var Dispatch
     */
    private $resource;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var CollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * @var PackageQtyUpdater
     */
    private $packageQtyUpdater;

    public function __construct(
        Dispatch $resource,
        DispatchRepository $dispatchRepository,
        CollectionFactory $packageCollectionFactory,
        PackageQtyUpdater $packageQtyUpdater
    ) {
        $this->resource = $resource;
        $this->dispatchRepository = $dispatchRepository;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->packageQtyUpdater = $packageQtyUpdater;
    }

    /**
     * Assign given tracks (packages) to given dispatch.
     *
     * @param int $dispatchId
     * @param int[]|string[] $trackIds
     * @throws LocalizedException
     */
    public function assign(int $dispatchId, array $trackIds): void
    {
        if (empty($trackIds)) {
            return;
        }

        $table = $this->resource->getTable('nrshipping_dispatch_package');

        // clean up existing entries
        $this->resource->getConnection()->delete($table, [
            'track_id IN (?)' => $trackIds,
        ]);

        // assign given tracks
        $data = array_map(
            static function ($trackId) use ($dispatchId) {
                return [
                    'track_id' => $trackId,
                    'dispatch_id' => $dispatchId,
                ];
            },
            $trackIds
        );

        $this->resource->getConnection()->insertMultiple($table, $data);

        $this->packageQtyUpdater->updateById($dispatchId);
    }

    /**
     * Remove given tracks (packages) from given dispatch.
     *
     * @param int $dispatchId
     * @param int[]|string[] $trackIds
     * @throws LocalizedException
     */
    public function unassign(int $dispatchId, array $trackIds): void
    {
        $table = $this->resource->getTable('nrshipping_dispatch_package');
        $this->resource->getConnection()->delete($table, [
            'dispatch_id = ?' => $dispatchId,
            'track_id IN (?)' => $trackIds,
        ]);

        $this->packageQtyUpdater->updateById($dispatchId);
    }

    /**
     * Assign all possible tracks (packages) to given dispatch.
     *
     * @param int $dispatchId
     * @throws LocalizedException
     */
    public function assignAll(int $dispatchId): void
    {
        try {
            $dispatch = $this->dispatchRepository->getById($dispatchId);
        } catch (NoSuchEntityException $exception) {
            return;
        }

        $collection = $this->packageCollectionFactory->create();
        $collection->setAssignableFilter($dispatch);
        $trackIds = $collection->getColumnValues('entity_id');

        $this->assign($dispatchId, $trackIds);

        $this->packageQtyUpdater->update($dispatch);
    }

    /**
     * Remove all tracks (packages) from given dispatch.
     *
     * @param int $dispatchId
     * @throws LocalizedException
     */
    public function unassignAll(int $dispatchId): void
    {
        $table = $this->resource->getTable('nrshipping_dispatch_package');
        $this->resource->getConnection()->delete($table, [
            'dispatch_id = ?' => $dispatchId,
        ]);

        $this->packageQtyUpdater->updateById($dispatchId);
    }
}
