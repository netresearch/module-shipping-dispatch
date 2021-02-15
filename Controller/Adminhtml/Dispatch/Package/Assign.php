<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch\Package;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Netresearch\ShippingDispatch\Model\Dispatch\PackageAssignment;
use Netresearch\ShippingDispatch\Model\ResourceModel\Package\CollectionFactory;

/**
 * POST action controller, assigns selected packages to a dispatch.
 */
class Assign extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Netresearch_ShippingDispatch::edit';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var PackageAssignment
     */
    private $packageAssignment;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PackageAssignment $packageAssignment
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->packageAssignment = $packageAssignment;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $dispatchId = (int) $this->_request->getParam('dispatch_id');
            $trackIds = $collection->getAllIds();

            $this->packageAssignment->assign($dispatchId, $trackIds);
        } catch (LocalizedException $exception) {
            $this->messageManager->addExceptionMessage($exception);
        }

        return $resultRedirect;
    }
}
