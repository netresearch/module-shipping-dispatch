<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ShippingDispatch\Model\BulkDispatch;

use Magento\Framework\Phrase;
use Magento\Framework\Phrase\Renderer\Placeholder;

/**
 * Collect messages during manifestation or cancellation.
 *
 * Use collected messages for logging, user output, etc.
 */
class MessageContainer
{
    private $messages = [];

    public function addMessage(int $trackId, Phrase $message): void
    {
        $this->messages[$trackId] = $message;
    }

    /**
     * @return Phrase[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Obtain un-localized messages
     *
     * @return string[]
     */
    public function getLogMessages(): array
    {
        $renderer = new Placeholder();

        return array_map(
            function (Phrase $message) use ($renderer) {
                return $renderer->render([$message->getText()], $message->getArguments());
            },
            $this->getMessages()
        );
    }
}
