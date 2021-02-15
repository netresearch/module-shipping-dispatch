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
use Magento\Store\Api\WebsiteRepositoryInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\DispatchRepository;

/**
 * Edit page controller for updating dispatch-track associations.
 */
class Edit extends Action
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
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    public function __construct(
        Context $context,
        DispatchRepository $dispatchRepository,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->dispatchRepository = $dispatchRepository;
        $this->websiteRepository = $websiteRepository;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $dispatchId = (int) $this->getRequest()->getParam('dispatch_id');

        try {
            $dispatch = $this->dispatchRepository->getById((int) $dispatchId);
            $website = $this->websiteRepository->getById($dispatch->getWebsiteId());
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addErrorMessage(__('This dispatch no longer exists.'));

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setRefererUrl();

            return $resultRedirect;
        }

        $viewStatus = [DispatchInterface::STATUS_PROCESSING, DispatchInterface::STATUS_COMPLETE];
        if (in_array($dispatch->getStatus(), $viewStatus)) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/view', ['dispatch_id' => $dispatch->getEntityId()]);

            return $resultRedirect;
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(
            __('Assign Packages for %1 in %2', $dispatch->getCarrierName(), $website->getName())
        );

        return $resultPage;
    }
}
