<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ShippingDispatch\Block\Adminhtml\DispatchEdit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Framework\UrlInterface;

class BackButton extends Button
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
        $backUrl = $this->urlBuilder->getUrl('*/dispatch/index');
        $this->setData('label', __('Back'));
        $this->setData('class', 'back');
        $this->setData('id', 'back');
        $this->setData('on_click', sprintf("setLocation('%s')", $backUrl));

        return parent::_beforeToHtml();
    }
}
