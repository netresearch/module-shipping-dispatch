<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchErrorResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchSuccessResponseInterface;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchManagement;
use Netresearch\ShippingDispatch\Model\BulkDispatch\MessageContainer;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Psr\Log\LoggerInterface;

/**
 * Manifest action controller for finalizing a pending dispatch.
 */
class Manifest extends Action implements HttpGetActionInterface
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
     * @var MessageContainer
     */
    private $messageContainer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        DispatchRepository $dispatchRepository,
        DispatchManagement $dispatchManagement,
        MessageContainer $messageContainer,
        LoggerInterface $logger
    ) {
        $this->dispatchRepository = $dispatchRepository;
        $this->dispatchManagement = $dispatchManagement;
        $this->messageContainer = $messageContainer;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();

        $dispatchId = (int) $this->getRequest()->getParam('dispatch_id');

        try {
            $dispatch = $this->dispatchRepository->getById($dispatchId);
            $dispatchResponses = $this->dispatchManagement->manifest([$dispatch]);
            $dispatchResponse = array_shift($dispatchResponses);
            if ($dispatchResponse instanceof DispatchSuccessResponseInterface) {
                $this->messageManager->addSuccessMessage(
                    __('Dispatch %1 was successfully manifested.', $dispatch->getDispatchNumber())
                );

                $resultRedirect->setPath('*/dispatch/view', ['dispatch_id' => $dispatch->getEntityId()]);
            } elseif ($dispatchResponse instanceof DispatchErrorResponseInterface) {
                $this->messageManager->addErrorMessage($dispatchResponse->getError());

                $resultRedirect->setPath('*/dispatch/edit', ['dispatch_id' => $dispatch->getEntityId()]);
            }

            foreach ($this->messageContainer->getMessages() as $message) {
                $this->messageManager->addWarningMessage($message);
            }

            return $resultRedirect;
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect;
    }
}
