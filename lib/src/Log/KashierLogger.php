<?php

namespace ITeam\Kashier\Log;

use ITeam\Kashier\Core\KashierConfigManager;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class KashierLogger extends AbstractLogger
{

    /**
     * @var array Indexed list of all log levels.
     */
    private $loggingLevels = array(
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG
    );

    /**
     * Configured Logging Level
     *
     * @var LogLevel $loggingLevel
     */
    private $loggingLevel;

    /**
     * Configured Logging File
     *
     * @var string
     */
    private $loggerFile;

    /**
     * Log Enabled
     *
     * @var bool
     */
    private $isLoggingEnabled;

    /**
     * Logger Name. Generally corresponds to class name
     *
     * @var string
     */
    private $loggerName;

    public function __construct($className)
    {
        $this->loggerName = $className;
        $this->initialize();
    }

    public function initialize()
    {
        $config = KashierConfigManager::getInstance()->getConfigHashmap();
        if (!empty($config)) {
            $this->isLoggingEnabled = (array_key_exists('log.LogEnabled', $config) && $config['log.LogEnabled'] == '1');
            if ($this->isLoggingEnabled) {
                $this->loggerFile = $config['log.FileName'] ?: ini_get('error_log');
                $loggingLevel = strtoupper($config['log.LogLevel']);
                $this->loggingLevel = (isset($loggingLevel) && defined("\\Psr\\Log\\LogLevel::$loggingLevel")) ?
                    constant("\\Psr\\Log\\LogLevel::$loggingLevel") :
                    LogLevel::INFO;
            }
        }
    }

    public function log($level, $message, array $context = array())
    {
        if ($this->isLoggingEnabled && array_search($level, $this->loggingLevels, true) <= array_search($this->loggingLevel, $this->loggingLevels, true)) {
            error_log('[' . date('d-m-Y H:i:s') . '] ' . $this->loggerName . ' : ' . strtoupper($level) . ": $message\n", 3, $this->loggerFile);
        }
    }
}
