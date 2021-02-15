<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingDispatch\Block\Adminhtml\DispatchEdit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\UrlInterface;
use Netresearch\ShippingDispatch\Controller\Adminhtml\Dispatch\Manifest;

class ManifestButton extends Button
{
    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
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
        $actionUrl = $this->urlBuilder->getUrl('*/dispatch/manifest', [
            'dispatch_id' => $this->_request->getParam('dispatch_id')
        ]);
        $this->setData('label', __('Manifest'));
        $this->setData('id', 'manifest');
        $this->setData('class', 'scalable primary');
        $this->setData('on_click', sprintf("setLocation('%s')", $actionUrl));

        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        if (!$this->_authorization->isAllowed(Manifest::ADMIN_RESOURCE)) {
            return '';
        }

        return parent::_toHtml();
    }
}
