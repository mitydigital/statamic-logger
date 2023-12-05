<?php

namespace MityDigital\StatamicLogger\Support;

use Illuminate\Log\Logger;

class LoggerFormatter
{
    /**
     * Customize the given logger instance.
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            // get the formatter, and disable inline breaks
            $formatter = $handler->getFormatter();
            $formatter->allowInlineLineBreaks(false);

            // re-set the formatter
            $handler->setFormatter($formatter);
        }
    }
}
