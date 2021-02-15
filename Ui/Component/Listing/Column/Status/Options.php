<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Ui\Component\Listing\Column\Status;

use Magento\Framework\Data\OptionSourceInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;

class Options implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        $optionArray = [];

        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    /**
     * Get options
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            DispatchInterface::STATUS_PENDING => __('Pending'),
            DispatchInterface::STATUS_PROCESSING => __('Processing'),
            DispatchInterface::STATUS_COMPLETE => __('Complete'),
            DispatchInterface::STATUS_FAILED => __('Failed'),
            DispatchInterface::STATUS_CANCELLED => __('Cancelled'),
        ];
    }
}
