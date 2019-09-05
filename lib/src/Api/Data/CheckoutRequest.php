<?php

namespace ITeam\Kashier\Api\Data;

use ITeam\Kashier\Common\KashierModel;

/**
 * Class CheckoutRequest
 *
 * A resource representing a Checkout request.
 * @package ITeam\Kashier\Api
 *
 * @property string hash
 * @property string serviceName
 * @property int expiry_year
 * @property int expiry_month
 * @property int ccv
 * @property string card_holder_name
 * @property string card_number
 * @property string merchantRedirect
 * @property string currency
 * @property string amount
 * @property string order
 * @property string merchantId
 * @property string mid
 * @property string cardToken
 * @property string shopper_reference
 * @property string display
 *
 */
class CheckoutRequest extends KashierModel
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
     * @return CheckoutRequest
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        $this->mid = $merchantId;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $order
     *
     * @return CheckoutRequest
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $amount
     *
     * @return CheckoutRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $currency
     *
     * @return CheckoutRequest
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantRedirect()
    {
        return $this->merchantRedirect;
    }

    /**
     * @param $merchantRedirect
     *
     * @return CheckoutRequest
     */
    public function setMerchantRedirect($merchantRedirect)
    {
        $this->merchantRedirect = $merchantRedirect;
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
     * @return CheckoutRequest
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
     * @return CheckoutRequest
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
     * @return CheckoutRequest
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
     * @return CheckoutRequest
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
     * @return CheckoutRequest
     */
    public function setExpiryYear($expiry_year)
    {
        $this->expiry_year = $expiry_year;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * @param $serviceName
     *
     * @return CheckoutRequest
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
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
     * @return CheckoutRequest
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getCardToken()
    {
        return $this->cardToken;
    }

    /**
     * @param string $cardToken
     * @return CheckoutRequest
     */
    public function setCardToken($cardToken)
    {
        $this->cardToken = $cardToken;

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
     * @return CheckoutRequest
     */
    public function setShopperReference($shopper_reference)
    {
        $this->shopper_reference = $shopper_reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * @param string $mid
     * @return CheckoutRequest
     */
    public function setMid($mid)
    {
        $this->mid = $mid;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplay()
    {
        return $this->display;
    }

    /**
     * @param string $display
     * @return CheckoutRequest
     */
    public function setDisplay($display)
    {
        $this->display = $display;

        return $this;
    }


}
