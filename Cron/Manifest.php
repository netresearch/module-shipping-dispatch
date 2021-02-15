<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Cron;

use Magento\Cron\Model\Schedule;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchLoader;
use Netresearch\ShippingDispatch\Model\BulkDispatch\DispatchManagement;
use Netresearch\ShippingDispatch\Model\BulkDispatch\MessageContainer;
use Netresearch\ShippingDispatch\Model\Config\Config;

class Manifest
{
    /**
     * @var Config
     */
    private $moduleConfig;

    /**
     * @var DispatchLoader
     */
    private $dispatchLoader;

    /**
     * @var DispatchManagement
     */
    private $dispatchManagement;

    /**
     * @var MessageContainer
     */
    private $messageContainer;

    public function __construct(
        Config $moduleConfig,
        DispatchLoader $dispatchLoader,
        DispatchManagement $dispatchManagement,
        MessageContainer $messageContainer
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->dispatchLoader = $dispatchLoader;
        $this->dispatchManagement = $dispatchManagement;
        $this->messageContainer = $messageContainer;
    }

    /**
     * Manifest all dispatches with pending status and assigned packages.
     *
     * @param Schedule $schedule
     */
    public function execute(Schedule $schedule): void
    {
        if (!$this->moduleConfig->isAutoManifestationEnabled()) {
            return;
        }

        $dispatches = array_filter(
            $this->dispatchLoader->getDispatches(DispatchInterface::STATUS_PENDING),
            function (DispatchInterface $dispatch) {
                return $dispatch->getPackageQty() > 0;
            }
        );

        if (empty($dispatches)) {
            return;
        }

        $this->dispatchManagement->manifest($dispatches);

        $messages = $this->messageContainer->getMessages();
        if (!empty($messages)) {
            $schedule->setMessages(implode(' | ', $messages));
        }
    }
}
