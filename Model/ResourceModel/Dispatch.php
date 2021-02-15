<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\Dispatch as DispatchModel;
use Netresearch\ShippingDispatch\Model\DispatchDocument as DocumentModel;
use Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument as DocumentResource;
use Netresearch\ShippingDispatch\Model\ResourceModel\DispatchDocument\CollectionFactory;
use Psr\Log\LoggerInterface;

class Dispatch extends AbstractDb
{
    /**
     * @var DocumentResource
     */
    private $documentResource;

    /**
     * @var CollectionFactory
     */
    private $documentCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        DocumentResource $documentResource,
        CollectionFactory $documentCollectionFactory,
        LoggerInterface $logger,
        $connectionName = null
    ) {
        $this->documentResource = $documentResource;
        $this->documentCollectionFactory = $documentCollectionFactory;
        $this->logger = $logger;

        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('nrshipping_dispatch', 'entity_id');
    }

    /**
     * Save associated dispatch documents in custom table.
     *
     * @param AbstractModel|DispatchModel $object
     * @return Dispatch
     */
    protected function _afterSave(AbstractModel $object)
    {
        $documents = $object->getDispatchDocuments();

        array_walk(
            $documents,
            function (DocumentModel $document) use ($object) {
                $document->setData(DispatchDocumentInterface::DISPATCH_ID, $object->getId());
                try {
                    $this->documentResource->save($document);
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                }
            }
        );

        return parent::_afterSave($object);
    }

    /**
     * Load associated dispatch documents from custom table.
     *
     * @param AbstractModel|DispatchModel $object
     * @return Dispatch
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $collection = $this->documentCollectionFactory->create();
        $collection->addFieldToFilter(DispatchDocumentInterface::DISPATCH_ID, $object->getId());

        $object->setData(DispatchInterface::DISPATCH_DOCUMENTS, $collection->getItems());

        return parent::_afterLoad($object);
    }
}
