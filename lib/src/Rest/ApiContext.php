<?php

namespace ITeam\Kashier\Rest;

use ITeam\Kashier\Core\KashierConfigManager;

/**
 * Class ApiContext
 *
 * Call level parameters such as credentials ... etc.
 *
 * @package ITeam\Kashier\Rest
 */
class ApiContext
{
    private $merchantId;

    /**
     * This is a placeholder for holding credential for the request
     * If the value is not set, it would get the value from @\ITeam\Kashier\Core\KashierCredentialManager
     *
     * @var \ITeam\Kashier\Auth\KashierKey
     */
    private $credential;


    /**
     * Construct
     *
     * @param string $merchantId
     * @param \ITeam\Kashier\Auth\KashierKey $credential
     */
    public function __construct($merchantId, $credential = null)
    {
        $this->merchantId = $merchantId;
        $this->credential = $credential;
    }

    public function getMerchantId() {
        return $this->merchantId;
    }

    /**
     * Get Credential
     *
     * @return \ITeam\Kashier\Auth\KashierKey
     */
    public function getCredential()
    {
        return $this->credential;
    }

    public function getRequestHeaders()
    {
        $result = KashierConfigManager::getInstance()->get('http.headers');
        $headers = array();
        foreach ($result as $header => $value) {
            $headerName = ltrim($header, 'http.headers');
            $headers[$headerName] = $value;
        }
        return $headers;
    }

    public function addRequestHeader($name, $value)
    {
        // Determine if the name already has a 'http.headers' prefix. If not, add one.
        if (!(stripos($name, 'http.headers') === 0)) {
            $name = 'http.headers.' . $name;
        }
        KashierConfigManager::getInstance()->addConfigs(array($name => $value));
    }

    /**
     * Sets Config
     *
     * @param array $config SDK configuration parameters
     */
    public function setConfig(array $config)
    {
        KashierConfigManager::getInstance()->addConfigs($config);
    }

    /**
     * Gets Configurations
     *
     * @return array
     */
    public function getConfig()
    {
        return KashierConfigManager::getInstance()->getConfigHashmap();
    }

    /**
     * Gets a specific configuration from key
     *
     * @param $searchKey
     * @return mixed
     */
    public function get($searchKey)
    {
        return KashierConfigManager::getInstance()->get($searchKey);
    }
}
