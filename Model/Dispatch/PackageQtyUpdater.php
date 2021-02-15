<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Dispatch;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Netresearch\ShippingDispatch\Model\ResourceModel\Package\CollectionFactory;

class PackageQtyUpdater
{
    /**
     * @var CollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    public function __construct(CollectionFactory $packageCollectionFactory, DispatchRepository $dispatchRepository)
    {
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->dispatchRepository = $dispatchRepository;
    }

    /**
     * Count packages currently assigned to the given dispatch and update it accordingly.
     *
     * @param DispatchInterface|Dispatch $dispatch
     * @throws CouldNotSaveException
     */
    public function update(DispatchInterface $dispatch): void
    {
        $collection = $this->packageCollectionFactory->create();
        $collection->addFieldToFilter('dispatch_id', ['eq' => $dispatch->getEntityId()]);
        $packageCount = $collection->getSize();

        $dispatch->setData(DispatchInterface::PACKAGE_QTY, $packageCount);
        $this->dispatchRepository->save($dispatch);
    }

    /**
     * Count packages currently assigned to the given dispatch ID and update it accordingly.
     *
     * @param int $dispatchId
     * @throws CouldNotSaveException
     */
    public function updateById(int $dispatchId): void
    {
        try {
            $dispatch = $this->dispatchRepository->getById($dispatchId);
        } catch (NoSuchEntityException $exception) {
            // dispatch not found, nothing to update
            return;
        }

        $this->update($dispatch);
    }
}
