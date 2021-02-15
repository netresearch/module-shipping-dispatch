<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;

class Actions extends Column
{
    /**
     * Prepare Data Source
     *
     * @param mixed[] $dataSource
     * @return mixed[]
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $viewStatus = [DispatchInterface::STATUS_PROCESSING, DispatchInterface::STATUS_COMPLETE];
                if (in_array($item[DispatchInterface::STATUS], $viewStatus)) {
                    $url = $this->context->getUrl('nrshipping/dispatch/view', ['dispatch_id' => $item['entity_id']]);
                    $item[$this->getData('name')]['view'] = [
                        'href' => $url,
                        'label' => __('View'),
                        'hidden' => false,
                        '__disableTmpl' => true
                    ];
                } else {
                    $url = $this->context->getUrl('nrshipping/dispatch/edit', ['dispatch_id' => $item['entity_id']]);
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $url,
                        'label' => __('Edit'),
                        'hidden' => false,
                        '__disableTmpl' => true
                    ];
                }
            }
        }

        return $dataSource;
    }
}
