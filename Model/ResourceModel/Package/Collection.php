<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\ResourceModel\Package;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track as TrackResource;
use Magento\Store\Model\StoreManagerInterface;
use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'nrshipping_dispatch_package_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'package_collection';

    /**
     * @var string[][]
     */
    protected $_map = [
        'fields' => [
            'entity_id' => 'main_table.entity_id',
            'dispatch_id' => 'assoc.dispatch_id',
            'store_id' => 'shipment.store_id',
        ]
    ];

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Set collection model and resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Track::class, TrackResource::class);
    }

    /**
     * Join tables.
     *
     * @return Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->joinShipment();
        $this->joinOrder();
        $this->joinShippingAddress();
        $this->joinDispatchAssociation();

        return $this;
    }

    private function joinShipment()
    {
        $this->getSelect()
             ->join(
                 ['shipment' => $this->getTable('sales_shipment')],
                 'main_table.parent_id = shipment.entity_id',
                 [
                     'store_id' => 'shipment.store_id',
                     'shipment_increment_id' => 'shipment.increment_id',
                 ]
             );
    }

    private function joinOrder()
    {
        $this->getSelect()
             ->join(
                 ['order' => $this->getTable('sales_order')],
                 'shipment.order_id = order.entity_id',
                 [
                     'shipping_description' => 'order.shipping_description',
                     'order_increment_id' => 'order.increment_id',
                 ]
             );
    }

    private function joinShippingAddress()
    {
        $this->getSelect()
             ->join(
                 ['address' => $this->getTable('sales_order_address')],
                 'order.entity_id = address.parent_id AND address_type = \'shipping\'',
                 [
                     'ship_to_name' => new \Zend_Db_Expr("CONCAT(address.firstname, ' ', address.lastname)"),
                 ]
             );
    }

    private function joinDispatchAssociation()
    {
        $this->getSelect()
             ->joinLeft(
                 ['assoc' => $this->getTable('nrshipping_dispatch_package')],
                 'main_table.entity_id = assoc.track_id'
             );
    }

    /**
     * Find tracks assignable to given dispatch.
     *
     * The only way to get the current dispatch's ID into the collection during UI loading
     * (mui/index/render) is to add it as filter url param. As we do not actually want the
     * listing to be filtered by this ID but only use it for adding other filters, we need
     * to manipulate the `where` condition before loading the collection.
     *
     * @param DispatchInterface $dispatch
     */
    public function setAssignableFilter(DispatchInterface $dispatch)
    {
        try {
            $website = $this->storeManager->getWebsite($dispatch->getWebsiteId());

            $where = array_map(
                static function (string $cond) {
                    if (strpos($cond, 'dispatch_id') === false) {
                        return $cond;
                    }

                    return preg_replace('/= \'[\d]+\'/', 'IS NULL', $cond);
                },
                $this->getSelect()->getPart(\Zend_Db_Select::WHERE)
            );

            $this->getSelect()->setPart(\Zend_Db_Select::WHERE, $where);

            $this->addFieldToFilter('carrier_code', ['eq' => $dispatch->getCarrierCode()]);
            $this->addFieldToFilter('store_id', ['in' => $website->getStoreIds()]);
        } catch (LocalizedException $exception) {
            $this->_logger->error($exception->getLogMessage(), ['exception' => $exception]);
        } catch (\Zend_Db_Select_Exception $exception) {
            $this->_logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }
}
