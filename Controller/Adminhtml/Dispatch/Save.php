<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterfaceFactory;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Netresearch\ShippingDispatch\Ui\Component\Form\Carrier\Options;
use Psr\Log\LoggerInterface;

/**
 * POST action controller, persists a new dispatch.
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Netresearch_ShippingDispatch::edit';

    /**
     * @var DispatchInterfaceFactory
     */
    private $dispatchFactory;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var Options
     */
    private $carrierTitles;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        DispatchInterfaceFactory $dispatchFactory,
        DispatchRepository $dispatchRepository,
        Options $carrierTitles,
        LoggerInterface $logger
    ) {
        $this->dispatchFactory = $dispatchFactory;
        $this->dispatchRepository = $dispatchRepository;
        $this->carrierTitles = $carrierTitles;
        $this->logger = $logger;

        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $websiteId =  $this->getRequest()->getParam('website_id');
        $carrierCode = $this->getRequest()->getParam('carrier_code');
        $carrierTitles = $this->carrierTitles->toArray((int) $websiteId);

        /** @var Dispatch $dispatch */
        $dispatch = $this->dispatchFactory->create();
        $dispatch->setData([
            DispatchInterface::WEBSITE_ID => $websiteId,
            DispatchInterface::CARRIER_NAME => $carrierTitles[$carrierCode],
            DispatchInterface::CARRIER_CODE => $carrierCode,
            DispatchInterface::STATUS => DispatchInterface::STATUS_PENDING,
        ]);

        try {
            $dispatch = $this->dispatchRepository->save($dispatch);
            $resultRedirect->setPath('*/dispatch/edit', ['dispatch_id' => $dispatch->getEntityId()]);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
            $this->messageManager->addErrorMessage($exception->getMessage());
            $resultRedirect->setRefererUrl();
        }

        return $resultRedirect;
    }
}
