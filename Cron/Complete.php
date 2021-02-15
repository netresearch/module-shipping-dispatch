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

class Complete
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
     * Complete all dispatches with processing status (fetch documentation).
     *
     * @param Schedule $schedule
     */
    public function execute(Schedule $schedule): void
    {
        if (!$this->moduleConfig->isAutoManifestationEnabled()) {
            return;
        }

        $dispatches = $this->dispatchLoader->getDispatches(DispatchInterface::STATUS_PROCESSING);

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
