<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingDispatch\Block\Adminhtml\DispatchEdit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch\Delete;

class DeleteButton extends Button
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Context $context,
        RequestInterface $request,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $data);
    }

    /**
     * Add button data
     *
     * @return Button
     */
    protected function _beforeToHtml()
    {
        $deleteAction = $this->urlBuilder->getUrl(
            '*/dispatch/delete',
            ['dispatch_id' => (int) $this->request->getParam('dispatch_id')]
        );

        $confirmationMsg = __('Are you sure you want to delete this dispatch?');
        $buttonData = [
            'label' => __('Delete'),
            'class' => 'delete',
            'id' => 'delete',
            'on_click' => "deleteConfirm('$confirmationMsg', '$deleteAction', {\"data\": {}})",
            'sort_order' => 20,
        ];

        $this->setData($buttonData);

        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        if (!$this->_authorization->isAllowed(Delete::ADMIN_RESOURCE)) {
            return '';
        }

        return parent::_toHtml();
    }
}
