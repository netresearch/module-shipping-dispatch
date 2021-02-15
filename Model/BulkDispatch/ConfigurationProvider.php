<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\BulkDispatch;

use Magento\Framework\Exception\LocalizedException;
use Netresearch\ShippingDispatch\Api\BulkDispatch\ConfigurationInterface;

class ConfigurationProvider
{
    /**
     * @var ConfigurationInterface[]
     */
    private $configurations;

    /**
     * ConfigurationProvider constructor.
     *
     * @param ConfigurationInterface[] $configurations
     */
    public function __construct(array $configurations = [])
    {
        $this->configurations = $configurations;
    }

    /**
     * @return ConfigurationInterface[]
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }

    /**
     * @param string $carrierCode
     * @return ConfigurationInterface
     * @throws LocalizedException
     */
    public function getConfiguration(string $carrierCode): ConfigurationInterface
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }

        throw new LocalizedException(__("Dispatch configuration for %1 is not available.", $carrierCode));
    }
}
