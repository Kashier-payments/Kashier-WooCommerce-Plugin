<?php

namespace ITeam\Kashier\Handler;

/**
 * Interface IKashierHandler
 *
 * @package ITeam\Kashier\Handler
 */
interface IKashierHandler
{
    /**
     *
     * @param \ITeam\Kashier\Core\KashierHttpConfig $httpConfig
     * @param string $request
     * @param mixed $options
     * @return mixed
     */
    public function handle($httpConfig, $request, $options);
}
