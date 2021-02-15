<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model;

use Netresearch\ShippingDispatch\Api\Data\DispatchInterface;

class DispatchRegistry
{
    /**
     * @var DispatchInterface
     */
    private $dispatch;

    public function setDispatch(DispatchInterface $dispatch): void
    {
        $this->dispatch = $dispatch;
    }

    public function getDispatch(): ?DispatchInterface
    {
        return $this->dispatch;
    }
}
