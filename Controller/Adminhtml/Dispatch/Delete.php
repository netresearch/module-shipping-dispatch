<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationErrorResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationSuccessResponseInterface;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchManagement;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\Dispatch\PackageAssignment;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Psr\Log\LoggerInterface;

/**
 * Delete action controller for deleting a dispatch with track associations.
 */
class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Netresearch_ShippingDispatch::edit';

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var DispatchManagement
     */
    private $dispatchManagement;

    /**
     * @var PackageAssignment
     */
    private $packageAssignment;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        DispatchManagement $dispatchManagement,
        DispatchRepository $dispatchRepository,
        PackageAssignment $packageAssignment,
        LoggerInterface $logger
    ) {
        $this->dispatchManagement = $dispatchManagement;
        $this->dispatchRepository = $dispatchRepository;
        $this->packageAssignment = $packageAssignment;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();

        $dispatchId = (int)$this->getRequest()->getParam('dispatch_id');

        try {
            /** @var Dispatch $dispatch */
            $dispatch = $this->dispatchRepository->getById($dispatchId);
            $cancelResponses = $this->dispatchManagement->cancel([$dispatch]);
            $cancelResponse = array_shift($cancelResponses);

            if ($cancelResponse instanceof CancellationSuccessResponseInterface) {
                $this->packageAssignment->unassignAll((int) $dispatch->getId());
                $this->dispatchRepository->delete($dispatch);
                $this->messageManager->addSuccessMessage(
                    __('Dispatch %1 was successfully deleted.', $dispatch->getDispatchNumber())
                );

                $resultRedirect->setPath('*/dispatch/index');
            } elseif ($cancelResponse instanceof CancellationErrorResponseInterface) {
                $this->messageManager->addErrorMessage($cancelResponse->getError());
            }
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect;
    }
}
