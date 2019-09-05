class KashierTokenization {
    constructor(merchantId, cardHolderName, cardNumber, ccv, expiryMonth, expiryYear, shopperReference, hash, tokenValidity = "temp") {
        this.merchantId = merchantId;
        this.card_holder_name = cardHolderName;
        this.card_number = cardNumber;
        this.ccv = ccv;
        this.expiry_month = expiryMonth;
        this.expiry_year = expiryYear;
        this.shopper_reference = shopperReference;
        this.tokenValidity = tokenValidity;
        this.hash = hash;
    }

    setHash(hash) {
        this.hash = hash; 
    }
}