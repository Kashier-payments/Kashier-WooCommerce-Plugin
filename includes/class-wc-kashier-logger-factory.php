<?php

use ITeam\Kashier\Log\KashierLogFactory;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Log all things!
 */
class WC_Kashier_Logger_Factory implements KashierLogFactory
{
    public function getLogger($className)
    {
        return new WC_Kashier_Logger();
    }
}
