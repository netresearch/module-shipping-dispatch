<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Ui\Component\Form\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingDispatch\Api\BulkDispatch\ConfigurationInterface;
use Netresearch\ShippingDispatch\Model\BulkDispatch\ConfigurationProvider;

/**
 * Carrier Options
 */
class Options implements OptionSourceInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigurationProvider
     */
    private $configurationProvider;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigurationProvider $configurationProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configurationProvider = $configurationProvider;
    }

    /**
     * Get options
     *
     * @return string[][]
     */
    public function toOptionArray(): array
    {
        $optionArray = [];
        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    /**
     * @param int $websiteId
     * @return string[]
     */
    public function toArray(int $websiteId = null): array
    {
        $carrierCodes = array_map(
            static function (ConfigurationInterface $carrierConfig) {
                return $carrierConfig->getCarrierCode();
            },
            $this->configurationProvider->getConfigurations()
        );

        $carrierCodes = array_combine($carrierCodes, $carrierCodes);

        // read carrier titles from config (scoped if possible)
        return array_map(
            function (string $carrierCode) use ($websiteId) {
                if ($websiteId) {
                    return (string)$this->scopeConfig->getValue(
                        "carriers/{$carrierCode}/title",
                        ScopeInterface::SCOPE_WEBSITE,
                        $websiteId
                    );
                } else {
                    return (string)$this->scopeConfig->getValue("carriers/{$carrierCode}/title");
                }
            },
            $carrierCodes
        );
    }
}
