<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public const CONFIG_PATH_ASSIGN_PACKAGES = 'shipping/batch_processing/dispatch/assign_packages';
    public const CONFIG_PATH_MANIFEST_DISPATCHES = 'shipping/batch_processing/dispatch/enable_manifestation';
    public const CONFIG_PATH_CRON_SCHEDULE = 'crontab/default/jobs/nrshipping_dispatch_manifest/schedule/cron_expr';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Obtain assign packages config setting.
     *
     * @param mixed $store
     * @return bool
     */
    public function isAutoAssignmentEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_ASSIGN_PACKAGES,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Obtain manifest dispatch config setting. Used in cron tasks, global only.
     *
     * @return bool
     */
    public function isAutoManifestationEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_MANIFEST_DISPATCHES);
    }
}
