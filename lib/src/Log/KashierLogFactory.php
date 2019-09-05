<?php

namespace ITeam\Kashier\Log;

use Psr\Log\LoggerInterface;

interface KashierLogFactory
{
    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger($className);
}
