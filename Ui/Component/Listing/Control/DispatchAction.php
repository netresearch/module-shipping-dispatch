<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Ui\Component\Listing\Control;

use Magento\Ui\Component\Control\Action;

class DispatchAction extends Action
{
    /**
     * Add the current page's dispatch id to the mass action url
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $config['url'] = $context->getUrl(
            $config['urlPath'],
            ['dispatch_id' => $context->getRequestParam('dispatch_id')]
        );
        $this->setData('config', (array)$config);
        parent::prepare();
    }
}
