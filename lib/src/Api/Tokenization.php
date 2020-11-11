<?php

namespace ITeam\Kashier\Api;

use ITeam\Kashier\Common\KashierResourceModel;
use ITeam\Kashier\Rest\ApiContext;
use ITeam\Kashier\Security\ICipher;
use ITeam\Kashier\Transport\KashierRestCall;

/**
 * Class Tokenization
 *
 * Lets you create a new checkout post request.
 *
 * @package ITeam\Kashier\Api
 *
 * @property \ITeam\Kashier\Api\Data\TokenizationRequest $tokenizationRequest
 * @property string $status
 * @property array $body
 * @property array error
 */
class Tokenization extends KashierResourceModel
{
    /**
     * @param Data\TokenizationRequest $tokenizationRequest
     *
     * @return self
     */
    public function setTokenizationRequest(Data\TokenizationRequest $tokenizationRequest)
    {
        $this->tokenizationRequest = $tokenizationRequest;

        return $this;
    }

    /**
     * Creates and processes a tokenization request.
     *
     * @param ApiContext $apiContext is the APIContext for this call. It can be used to pass dynamic configuration and credentials.
     * @param ICipher $cipher
     * @param KashierRestCall $restCall is the Rest Call Service that is used to make rest calls
     *
     * @return self
     * @throws \ITeam\Kashier\Exception\KashierConfigurationException
     * @throws \ITeam\Kashier\Exception\KashierConnectionException
     */
    public function send($apiContext, ICipher $cipher, $restCall = null)
    {
        $this->getTokenizationRequest()
            ->setMerchantId($apiContext->getMerchantId())
            ->setHash($cipher->encrypt());

        $payLoad = $this->getTokenizationRequest()->toJSON();
        $json = self::executeCall(
            '/tokenization',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        $this->fromJson($json);

        return $this;
    }

    /**
     * @return Data\TokenizationRequest
     */
    public function getTokenizationRequest()
    {
        return $this->tokenizationRequest;
    }

    public function isSuccess()
    {
        return strtoupper($this->getStatus()) === 'SUCCESS';
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $body = $this->getBody();
        return $body['status'];
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getError()
    {
        return $this->error;
    }

    public function getErrorMessage()
    {
        $error = $this->getError();

        if (isset($error['explanation'])) {
            return $error['explanation'];
        }

        return null;
    }
}
