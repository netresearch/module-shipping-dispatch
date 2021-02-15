<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\ResourceModel\Dispatch as DispatchResource;

class DispatchRepository
{
    /**
     * @var DispatchResource
     */
    private $resource;

    /**
     * @var DispatchFactory
     */
    private $dispatchFactory;

    public function __construct(
        DispatchResource $resource,
        DispatchFactory $dispatchFactory
    ) {
        $this->resource = $resource;
        $this->dispatchFactory = $dispatchFactory;
    }

    /**
     * @param int $id
     * @return DispatchInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): DispatchInterface
    {
        $dispatch = $this->dispatchFactory->create();
        $this->resource->load($dispatch, $id);
        if (!$dispatch->getId()) {
            throw new NoSuchEntityException(__('Unable to find dispatch with ID %1.', $id));
        }

        return $dispatch;
    }

    /**
     * Persist the dispatch object.
     *
     * @param DispatchInterface|Dispatch $dispatch
     * @return DispatchInterface
     * @throws CouldNotSaveException
     */
    public function save(DispatchInterface $dispatch): DispatchInterface
    {
        try {
            $this->resource->save($dispatch);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Unable to save dispatch.'), $exception);
        }

        return $dispatch;
    }

    /**
     * @param DispatchInterface|Dispatch $dispatch
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(DispatchInterface $dispatch): bool
    {
        try {
            $this->resource->delete($dispatch);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Unable to delete dispatch.'), $exception);
        }

        return true;
    }
}
