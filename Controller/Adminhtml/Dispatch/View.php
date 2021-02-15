<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchSuccessResponseInterface;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchManagement;
use Netresearch\ShippingDispatch\Model\DispatchRegistry;
use Netresearch\ShippingDispatch\Model\DispatchRepository;

/**
 * View page controller for accessing dispatch documentation.
 */
class View extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Netresearch_ShippingDispatch::view';

    /**
     * @var DispatchManagement
     */
    private $dispatchManagement;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var DispatchRegistry
     */
    private $dispatchRegistry;

    public function __construct(
        Context $context,
        DispatchManagement $dispatchManagement,
        DispatchRepository $dispatchRepository,
        DispatchRegistry $dispatchRegistry
    ) {
        $this->dispatchManagement = $dispatchManagement;
        $this->dispatchRepository = $dispatchRepository;
        $this->dispatchRegistry = $dispatchRegistry;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $dispatchId = (int) $this->getRequest()->getParam('dispatch_id');

        try {
            $dispatch = $this->dispatchRepository->getById((int) $dispatchId);
            if ($dispatch->getStatus() === DispatchInterface::STATUS_PROCESSING) {
                // try completing dispatch
                $dispatchResponses = $this->dispatchManagement->manifest([$dispatch]);
                $dispatchResponse = array_shift($dispatchResponses);

                if (($dispatchResponse instanceof DispatchSuccessResponseInterface)
                    && ($dispatchResponse->getDispatch()->getStatus() === DispatchInterface::STATUS_COMPLETE)) {
                    $this->messageManager->addNoticeMessage(__('Dispatch documentation was updated.'));
                }
            }

            /** @var Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $resultPage->getConfig()->getTitle()->prepend(__('Dispatch %1', $dispatch->getDispatchNumber()));

            $this->dispatchRegistry->setDispatch($dispatch);

            return $resultPage;
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This dispatch no longer exists.'));

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererUrl();

            return $resultRedirect;
        }
    }
}
