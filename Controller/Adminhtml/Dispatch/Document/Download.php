<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch\Document;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Netresearch\ShippingDispatch\Api\Data\DispatchDocumentInterface;
use Netresearch\ShippingDispatch\Model\DispatchDocumentRepository;
use Psr\Log\LoggerInterface;

/**
 * Download controller, offers a dispatch document for download.
 */
class Download extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Netresearch_ShippingDispatch::view';

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var DispatchDocumentRepository
     */
    private $documentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DispatchDocumentRepository $documentRepository,
        LoggerInterface $logger
    ) {
        $this->fileFactory = $fileFactory;
        $this->documentRepository = $documentRepository;
        $this->logger = $logger;

        parent::__construct($context);
    }

    private function getFileName(DispatchDocumentInterface $document)
    {
        $search = ['/\s/', '/[^a-z0-9-]/'];
        $replace = ['-', ''];
        $fileName = preg_filter($search, $replace, strtolower($document->getName()));
        $fileExt = strtolower($document->getFormat());

        return sprintf('%s.%s', $fileName, $fileExt);
    }

    private function getMimeType(DispatchDocumentInterface $document)
    {
        $format = strtolower($document->getFormat());
        switch ($format) {
            case 'zip':
                return 'application/zip';
            case 'pdf':
                return 'application/pdf';
            case 'png':
                return 'image/png';
            case 'jpg':
                return 'image/jpeg';
            case 'zpl':
                return 'x-application/zpl';
            default:
                return 'application/octet-stream';
        }
    }

    public function execute(): ?ResponseInterface
    {
        $documentId = (int) $this->getRequest()->getParam('document_id');

        try {
            $document = $this->documentRepository->get($documentId);
            return $this->fileFactory->create(
                $this->getFileName($document),
                $document->getContent(),
                DirectoryList::ROOT,
                $this->getMimeType($document)
            );
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
            $this->_forward('noroute');
            return null;
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            $this->_forward('noroute');
            return null;
        }
    }
}
