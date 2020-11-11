<?php

namespace ITeam\Kashier\Auth;

use ITeam\Kashier\Common\KashierModel;

/**
 * Class KashierKey
 */
class KashierKey extends KashierModel {
    /**
     * Client secret as obtained from the developer portal
     *
     * @var string $apiKey
     */
    private $apiKey;

    /**
     * Construct
     *
     * @param string $apiKey client secret obtained from the developer portal
     * @throws \ITeam\Kashier\Exception\KashierConfigurationException
     */
    public function __construct( $apiKey ) {
        parent::__construct();
        $this->apiKey = $apiKey;
    }

    /**
     * Get Client Secret
     *
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }
}
