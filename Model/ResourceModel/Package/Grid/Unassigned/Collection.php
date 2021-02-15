<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel\Package\Grid\Unassigned;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Netresearch\ShippingDispatch\Model\ResourceModel\Package\SearchResult;
use Psr\Log\LoggerInterface;

class Collection extends SearchResult
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'nrshipping_dispatch_unassigned_packages_grid_collection';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        DispatchRepository $dispatchRepository,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->request = $request;
        $this->dispatchRepository = $dispatchRepository;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        $dispatchId = $this->request->getParam('dispatch_id');
        if (!$dispatchId) {
            return $this;
        }

        try {
            $dispatch = $this->dispatchRepository->getById((int) $dispatchId);
            $this->setAssignableFilter($dispatch);
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }

        return $this;
    }
}
