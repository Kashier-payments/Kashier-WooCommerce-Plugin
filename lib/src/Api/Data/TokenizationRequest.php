<?php

namespace ITeam\Kashier\Api\Data;

use ITeam\Kashier\Common\KashierModel;

/**
 * Class TokenizationRequest
 *
 * A resource representing a Tokenization request.
 * @package ITeam\Kashier\Api
 *
 * @property string shopper_reference
 * @property string hash
 * @property string expiry_year
 * @property string expiry_month
 * @property string ccv
 * @property string card_holder_name
 * @property string card_number
 * @property string tokenValidity
 * @property string merchantId
 *
 */
class TokenizationRequest extends KashierModel
{
    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param $merchantId
     *
     * @return TokenizationRequest
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTokenValidity()
    {
        return $this->tokenValidity;
    }

    /**
     * @param $tokenValidity
     *
     * @return TokenizationRequest
     */
    public function setTokenValidity($tokenValidity)
    {
        $this->tokenValidity = $tokenValidity;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardNumber()
    {
        return $this->card_number;
    }

    /**
     * @param $card_number
     *
     * @return TokenizationRequest
     */
    public function setCardNumber($card_number)
    {
        $this->card_number = $card_number;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardHolderName()
    {
        return $this->card_holder_name;
    }

    /**
     * @param $card_holder_name
     *
     * @return TokenizationRequest
     */
    public function setCardHolderName($card_holder_name)
    {
        $this->card_holder_name = $card_holder_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getCcv()
    {
        return $this->ccv;
    }

    /**
     * @param $ccv
     *
     * @return TokenizationRequest
     */
    public function setCcv($ccv)
    {
        $this->ccv = $ccv;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiryMonth()
    {
        return $this->expiry_month;
    }

    /**
     * @param $expiry_month
     *
     * @return TokenizationRequest
     */
    public function setExpiryMonth($expiry_month)
    {
        $this->expiry_month = $expiry_month;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpiryYear()
    {
        return $this->expiry_year;
    }

    /**
     * @param $expiry_year
     *
     * @return TokenizationRequest
     */
    public function setExpiryYear($expiry_year)
    {
        $this->expiry_year = $expiry_year;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param $hash
     *
     * @return TokenizationRequest
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getShopperReference()
    {
        return $this->shopper_reference;
    }

    /**
     * @param string $shopper_reference
     * @return TokenizationRequest
     */
    public function setShopperReference($shopper_reference)
    {
        $this->shopper_reference = $shopper_reference;

        return $this;
    }
}
