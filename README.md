# Netresearch Shipping Dispatch Extension

The _Netresearch Shipping Dispatch_ extension provides a framework for
dispatching shipments. Carrier modules can build upon it to provide
shipment manifestation as part of their shipping fulfillment process.

## Description

The extension introduces a new _Dispatch_ entity. A dispatch collects
packages/labels that were created for shipments. The dispatch can then
be manifested for preparing the handover of packages to the courier/postal
facility. This is also referred to as close-out. Depending on the carrier
capabilities, this usually results in some sort of dispatch documentation
(handover note, manifest).

The extension provides data schema and UI integration. The necessary API
calls (manifest labels, download documentation, cancel manifestation) are
to be implemented by the carrier module.

Requirements
------------
* PHP >= 7.1.0

Compatibility
-------------
* Magento >= 2.3.0+

## Installation Instructions

Install sources:

    composer require netresearch/module-shipping-dispatch

Enable module:

    ./bin/magento module:enable Netresearch_ShippingDispatch
    ./bin/magento setup:upgrade

Flush cache and compile:

    ./bin/magento cache:flush
    ./bin/magento setup:di:compile

## Uninstallation

To unregister the module from the application, run the following command:

    ./bin/magento module:uninstall --remove-data Netresearch_ShippingDispatch
    composer update

This will automatically remove source files, clean up the database, update package dependencies.

To clean up the database manually, run the following commands:

    DROP TABLE `nrshipping_dispatch_document`, `nrshipping_dispatch_package`, `nrshipping_dispatch`;
    DELETE FROM `core_config_data` WHERE `path` LIKE 'shipping/batch_processing/dispatch/%';

## License

[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

## Copyright

(c) 2021 Netresearch DTT GmbH

