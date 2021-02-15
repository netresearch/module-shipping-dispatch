<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Netresearch\ShippingDispatch\Model\Config\Config;

class Cron extends Value
{
    /**
     * @var WriterInterface
     */
    private $configWriter;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        WriterInterface $configWriter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->configWriter = $configWriter;

        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Converts configured time into a cron expression and saves it to the cron job configuration.
     *
     * @return $this
     */
    public function afterSave(): self
    {
        $time = explode(',', $this->getValue());

        $minute = (int) ltrim($time[1], '0');
        $hour = (int) ltrim($time[0], '0');

        $this->configWriter->save(Config::CONFIG_PATH_CRON_SCHEDULE, "$minute $hour * * *");

        return parent::afterSave();
    }
}
