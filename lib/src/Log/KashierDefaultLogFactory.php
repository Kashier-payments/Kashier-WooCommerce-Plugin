<?php

namespace ITeam\Kashier\Log;

use Psr\Log\LoggerInterface;

/**
 * Class KashierDefaultLogFactory
 *
 * This factory is the default implementation of Log factory.
 *
 * @package ITeam\Kashier\Log
 */
class KashierDefaultLogFactory implements KashierLogFactory
{
    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger($className)
    {
        return new KashierLogger($className);
    }
}
