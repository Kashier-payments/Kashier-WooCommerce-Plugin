<?php

namespace ITeam\Kashier\Security;

use ITeam\Kashier\Api\Data\CheckoutRequest;
use ITeam\Kashier\Api\Data\TokenizationRequest;
use ITeam\Kashier\Rest\ApiContext;

/**
 * Class TokenizationRequestCipher
 *
 * Helper class to encrypt data with api key
 *
 * @package ITeam\Kashier\Security
 */
class TokenizationRequestCipher implements ICipher
{
    private $tokenizationRequest;
    private $apiContext;

    public function __construct(ApiContext $apiContext, TokenizationRequest $tokenizationRequest)
    {
        $this->apiContext = $apiContext;
        $this->tokenizationRequest = $tokenizationRequest;
    }

    /**
     * Encrypts the input text using the cipher key
     *
     * @return string
     */
    public function encrypt()
    {
        $path = '/?payment='
            . $this->apiContext->getMerchantId()
            . '.'
            . $this->tokenizationRequest->getShopperReference();

        return hash_hmac('sha256', $path, $this->apiContext->getCredential()->getApiKey(), false);
    }
}
