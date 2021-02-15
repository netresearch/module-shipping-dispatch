<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Ui\Component\Form\Website;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Escaper;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Website Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Escaper
     */
    protected $escaper;

    public function __construct(
        StoreManagerInterface $storeManager,
        Escaper $escaper
    ) {
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * Get options
     *
     * @return string[][]
     */
    public function toOptionArray(): array
    {
        return array_map(
            function (WebsiteInterface $website) {
                return [
                    'value' => $website->getId(),
                    'label' => $this->escaper->escapeHtml($website->getName()),
                ];
            },
            $this->storeManager->getWebsites()
        );
    }
}
