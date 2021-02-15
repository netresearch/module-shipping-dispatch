<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\BulkDispatch;

use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchResponseInterface;
use Psr\Log\LoggerInterface;

class DispatchManagement
{
    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    /**
     * @var DispatchResponseProcessor
     */
    private $responseProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ConfigurationProvider $configurationProvider,
        DispatchResponseProcessor $responseProcessor,
        LoggerInterface $logger
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->responseProcessor = $responseProcessor;
        $this->logger = $logger;
    }

    /**
     * @param DispatchInterface[] $dispatches
     * @return DispatchInterface[][]
     */
    private function getGroupedDispatches(array $dispatches): array
    {
        return array_reduce(
            $dispatches,
            static function (array $carrierDispatches, DispatchInterface $dispatch) {
                $carrierDispatches[$dispatch->getCarrierCode()][] = $dispatch;
                return $carrierDispatches;
            },
            []
        );
    }

    /**
     * @param DispatchInterface[] $dispatches
     * @return DispatchResponseInterface[]
     */
    public function manifest(array $dispatches): array
    {
        $carrierResults = [];

        foreach ($this->getGroupedDispatches($dispatches) as $carrierCode => $dispatches) {
            try {
                $configuration = $this->configurationProvider->getConfiguration($carrierCode);
                $carrierResults[$carrierCode] = $configuration->getDispatchManagement()->manifest($dispatches);
            } catch (LocalizedException $exception) {
                $msg = "Bulk dispatch manifestation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }
        }

        foreach ($carrierResults as $carrierCode => $carrierResult) {
            foreach ($carrierResult as $dispatchResponse) {
                $this->responseProcessor->processDispatchResponse($dispatchResponse);
            }
        }

        // return flat response, drop carrier code index
        return array_reduce($carrierResults, 'array_merge', []);
    }

    /**
     * @param DispatchInterface[] $dispatches
     * @return CancellationResponseInterface[]
     */
    public function cancel(array $dispatches): array
    {
        $carrierResults = [];

        foreach ($this->getGroupedDispatches($dispatches) as $carrierCode => $dispatches) {
            try {
                $configuration = $this->configurationProvider->getConfiguration($carrierCode);
                $carrierResults[$carrierCode] = $configuration->getDispatchManagement()->cancel($dispatches);
            } catch (LocalizedException $exception) {
                $msg = "Bulk dispatch cancellation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }
        }

        foreach ($carrierResults as $carrierCode => $carrierResult) {
            foreach ($carrierResult as $dispatchResponse) {
                $this->responseProcessor->processCancellationResponse($dispatchResponse);
            }
        }

        // return flat response, drop carrier code index
        return array_reduce($carrierResults, 'array_merge', []);
    }
}
