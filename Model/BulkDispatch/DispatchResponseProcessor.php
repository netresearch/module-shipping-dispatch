<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\BulkDispatch;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterfaceFactory;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\CancellationSuccessResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchErrorResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DispatchSuccessResponseInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\PackageErrorInterface;
use Netresearch\ShippingDispatch\Model\Dispatch;
use Netresearch\ShippingDispatch\Model\Dispatch\PackageAssignment;
use Netresearch\ShippingDispatch\Model\DispatchDocument;
use Netresearch\ShippingDispatch\Model\DispatchRepository;
use Psr\Log\LoggerInterface;

/**
 * Perform action on the carrier's dispatch response
 */
class DispatchResponseProcessor
{
    /**
     * @var DispatchDocumentInterfaceFactory
     */
    private $documentFactory;

    /**
     * @var PackageAssignment
     */
    private $packageAssignment;

    /**
     * @var MessageContainer
     */
    private $messageContainer;

    /**
     * @var DispatchRepository
     */
    private $dispatchRepository;

    /**
     * @var DateTime
     */
    public $dateTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        DispatchDocumentInterfaceFactory $documentFactory,
        PackageAssignment $packageAssignment,
        MessageContainer $messageContainer,
        DispatchRepository $dispatchRepository,
        DateTime $dateTime,
        LoggerInterface $logger
    ) {
        $this->documentFactory = $documentFactory;
        $this->packageAssignment = $packageAssignment;
        $this->messageContainer = $messageContainer;
        $this->dispatchRepository = $dispatchRepository;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Convert carrier's response documents to dispatch documents (shown on dispatch details view)
     *
     * @param DispatchInterface $dispatch
     * @param DocumentInterface[] $carrierDocuments
     * @return DispatchDocumentInterface[]
     */
    private function createDocuments(DispatchInterface $dispatch, array $carrierDocuments): array
    {
        return array_map(
            function (DocumentInterface $carrierDocument) use ($dispatch) {
                /** @var DispatchDocument $document */
                $document = $this->documentFactory->create();
                $document->setData([
                    DispatchDocumentInterface::DISPATCH_ID => $dispatch->getEntityId(),
                    DispatchDocumentInterface::NAME => $carrierDocument->getName(),
                    DispatchDocumentInterface::FORMAT => $carrierDocument->getFormat(),
                    DispatchDocumentInterface::CONTENT => $carrierDocument->getContent(),
                ]);

                return $document;
            },
            $carrierDocuments
        );
    }

    /**
     * Remove failed tracks from dispatch, register an error message for interface output.
     *
     * @param DispatchInterface $dispatch
     * @param ShipmentTrackInterface[] $tracks
     * @param PackageErrorInterface[] $carrierErrors
     */
    private function handleErrors(DispatchInterface $dispatch, array $tracks, array $carrierErrors): void
    {
        if (empty($carrierErrors)) {
            return;
        }

        $messages = [];
        foreach ($carrierErrors as $carrierError) {
            $messages[$carrierError->getTrackId()] = $carrierError->getErrorMessage();
        }

        try {
            $this->packageAssignment->unassign($dispatch->getEntityId(), array_keys($messages));
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage());
        }

        foreach ($tracks as $track) {
            if (array_key_exists($track->getEntityId(), $messages)) {
                $message = $messages[$track->getEntityId()];
                $this->messageContainer->addMessage(
                    (int) $track->getEntityId(),
                    __('Manifestation error for package %1: %2', $track->getTrackNumber(), $message)
                );
            }
        }
    }

    /**
     * Update dispatch with manifestation result.
     *
     * @param DispatchResponseInterface $carrierResponse
     */
    public function processDispatchResponse(DispatchResponseInterface $carrierResponse): void
    {
        /** @var Dispatch $dispatch */
        $dispatch = $carrierResponse->getDispatch();
        if ($carrierResponse instanceof DispatchSuccessResponseInterface) {
            // handle invalid packages (partial success: some tracks failed during an otherwise valid request)
            $this->handleErrors($dispatch, $carrierResponse->getTracks(), $carrierResponse->getDispatchErrors());

            // update dispatch data
            $documents = $this->createDocuments($dispatch, $carrierResponse->getDispatchDocuments());
            $status = empty($documents) ? DispatchInterface::STATUS_PROCESSING : DispatchInterface::STATUS_COMPLETE;
            $dispatch->addData([
                DispatchInterface::DISPATCH_NUMBER => $carrierResponse->getDispatchNumber(),
                DispatchInterface::DISPATCH_DATE => $this->dateTime->gmtDate(null, $carrierResponse->getDispatchDate()),
                DispatchInterface::STATUS => $status,
                DispatchInterface::DISPATCH_DOCUMENTS => $documents
            ]);
        } elseif ($carrierResponse instanceof DispatchErrorResponseInterface) {
            // update dispatch data
            $dispatch->setData(DispatchInterface::STATUS, DispatchInterface::STATUS_FAILED);
        }

        try {
            $this->dispatchRepository->save($dispatch);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }

    /**
     * Update dispatch with cancellation result.
     *
     * @param CancellationResponseInterface $carrierResponse
     */
    public function processCancellationResponse(CancellationResponseInterface $carrierResponse): void
    {
        /** @var Dispatch $dispatch */
        $dispatch = $carrierResponse->getDispatch();

        if ($carrierResponse instanceof CancellationSuccessResponseInterface) {
            $dispatch->setData(DispatchInterface::STATUS, DispatchInterface::STATUS_CANCELLED);
        }

        try {
            $this->dispatchRepository->save($dispatch);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
