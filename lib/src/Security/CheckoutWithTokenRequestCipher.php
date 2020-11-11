<?php

namespace ITeam\Kashier\Security;

use ITeam\Kashier\Api\Data\CheckoutRequest;
use ITeam\Kashier\Rest\ApiContext;

/**
 * Class Cipher
 *
 * Helper class to encrypt data with api key
 *
 * @package ITeam\Kashier\Security
 */
class CheckoutWithTokenRequestCipher implements ICipher
{
    private $checkoutRequest;
    private $apiContext;

    public function __construct(ApiContext $apiContext, CheckoutRequest $checkoutRequest)
    {
        $this->apiContext = $apiContext;
        $this->checkoutRequest = $checkoutRequest;
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
            . $this->checkoutRequest->getOrderId()
            . '.'
            . $this->checkoutRequest->getAmount()
            . '.'
            . $this->checkoutRequest->getCurrency()
            . '.'
            . $this->checkoutRequest->getShopperReference();

        return hash_hmac('sha256', $path, $this->apiContext->getCredential()->getApiKey(), false);
    }
}
