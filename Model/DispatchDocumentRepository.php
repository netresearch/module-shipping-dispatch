<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterfaceFactory;
use Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument as DispatchDocumentResource;

class DispatchDocumentRepository
{
    /**
     * @var DispatchDocumentResource
     */
    private $documentResource;

    /**
     * @var DispatchDocumentInterfaceFactory
     */
    private $documentFactory;

    public function __construct(
        DispatchDocumentResource $documentResource,
        DispatchDocumentInterfaceFactory $documentFactory
    ) {
        $this->documentResource = $documentResource;
        $this->documentFactory = $documentFactory;
    }

    /**
     * @param int $id
     * @return DispatchDocumentInterface
     * @throws NoSuchEntityException
     */
    public function get(int $id): DispatchDocumentInterface
    {
        /** @var DispatchDocument $document */
        $document = $this->documentFactory->create();
        $this->documentResource->load($document, $id);
        if (!$document->getId()) {
            throw new NoSuchEntityException(__('Unable to find dispatch document with ID %1.', $id));
        }

        return $document;
    }
}
