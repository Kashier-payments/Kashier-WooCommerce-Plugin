<?php

namespace ITeam\Kashier\Exception;

/**
 * Class KashierConfigurationException
 *
 * @package ITeam\Kashier\Exception
 */
class KashierConfigurationException extends \Exception
{

    /**
     * Default Constructor
     *
     * @param string|null $message
     * @param int  $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
