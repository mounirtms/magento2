<?php

namespace Mustafa\YalidineDelivery\Logger;
use Psr\Log\LoggerInterface;

class DebugLogger
{
    protected LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
     * Logs a debug message.
     *
     * @param string $message The message to info
     * @param array  $context  Additional context data for the log entry
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
