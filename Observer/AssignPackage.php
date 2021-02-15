<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment\Track;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterfaceFactory;
use Netresearch\ShippingDispatch\Model\BulkDispatch\ConfigurationProvider;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchLoader;
use Netresearch\ShippingDispatch\Model\Config\Config;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\Dispatch\PackageAssignment;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Netresearch\ShippingDispatch\Ui\Component\Form\Carrier\Options;
use Psr\Log\LoggerInterface;

/**
 * Assign package to dispatch.
 */
class AssignPackage implements ObserverInterface
{
    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * @var ConfigurationProvider
     */
    private $configProvider;

    /**
     * @var PackageAssignment
     */
    private $packageAssignment;

    /**
     * @var DispatchInterfaceFactory
     */
    private $dispatchFactory;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var DispatchLoader
     */
    private $dispatchLoader;

    /**
     * @var Options
     */
    private $carrierTitles;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Config $moduleConfig,
        ConfigurationProvider $configProvider,
        DispatchLoader $dispatchLoader,
        DispatchInterfaceFactory $dispatchFactory,
        DispatchRepository $dispatchRepository,
        Options $carrierTitles,
        PackageAssignment $packageAssignment,
        LoggerInterface $logger
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->configProvider = $configProvider;
        $this->dispatchLoader = $dispatchLoader;
        $this->dispatchFactory = $dispatchFactory;
        $this->dispatchRepository = $dispatchRepository;
        $this->carrierTitles = $carrierTitles;
        $this->packageAssignment = $packageAssignment;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        if (!$this->moduleConfig->isAutoAssignmentEnabled()) {
            return;
        }

        /** @var Track $track */
        $track = $observer->getData('track');

        try {
            $carrierConfiguration = $this->configProvider->getConfiguration($track->getCarrierCode());
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
            return;
        }

        $websiteId = (int) $track->getStore()->getWebsiteId();

        $dispatch = $this->dispatchLoader->getPendingDispatch($carrierConfiguration->getCarrierCode(), $websiteId);
        if (!$dispatch instanceof DispatchInterface) {
            // no pending dispatch found for carrier, create one.
            $carrierTitles = $this->carrierTitles->toArray((int) $websiteId);

            /** @var Dispatch $dispatch */
            $dispatch = $this->dispatchFactory->create();
            $dispatch->setData([
                DispatchInterface::WEBSITE_ID => $websiteId,
                DispatchInterface::CARRIER_NAME => $carrierTitles[$carrierConfiguration->getCarrierCode()],
                DispatchInterface::CARRIER_CODE => $carrierConfiguration->getCarrierCode(),
                DispatchInterface::STATUS => DispatchInterface::STATUS_PENDING,
            ]);

            try {
                $this->dispatchRepository->save($dispatch);
            } catch (CouldNotSaveException $exception) {
                $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
                return;
            }
        }

        try {
            $this->packageAssignment->assign($dispatch->getEntityId(), [$track->getId()]);
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
