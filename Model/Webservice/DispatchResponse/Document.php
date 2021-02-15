<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\Webservice\DispatchResponse;

use Netresearch\ShippingDispatch\Api\Data\DispatchResponse\DocumentInterface;

class Document implements DocumentInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $format;

    public function __construct(
        string $name,
        string $content,
        string $format
    ) {
        $this->name = $name;
        $this->content = $content;
        $this->format = $format;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function getContent(): string
    {
        return (string) $this->content;
    }

    public function getFormat(): string
    {
        return (string) $this->format;
    }
}
