<?php

namespace ITeam\Kashier\Common;

use ITeam\Kashier\Handler\RestHandler;
use ITeam\Kashier\Rest\ApiContext;
use ITeam\Kashier\Rest\IResource;
use ITeam\Kashier\Transport\KashierRestCall;

/**
 * Class KashierResourceModel
 * An Executable KashierModel Class
 *
 * @package ITeam\Kashier\Common
 */
class KashierResourceModel extends KashierModel implements IResource
{
    /**
     * Execute SDK Call to Kashier services
     *
     * @param string $url
     * @param string $method
     * @param string $payLoad
     * @param array $headers
     * @param ApiContext $apiContext
     * @param KashierRestCall $restCall
     * @param array $handlers
     * @return string json response of the object
     * @throws \ITeam\Kashier\Exception\KashierConfigurationException
     * @throws \ITeam\Kashier\Exception\KashierConnectionException
     */
    protected static function executeCall($url, $method, $payLoad, $headers = array(), $apiContext = null, $restCall = null, $handlers = [RestHandler::class])
    {
        //Initialize the context and rest call object if not provided explicitly
        $apiContext = $apiContext ?: new ApiContext(self::$credential);
        $restCall = $restCall ?: new KashierRestCall($apiContext);

        //Make the execution call
        return $restCall->execute($url, $method, $payLoad, $handlers, $headers);
    }
}
