class KashierCheckout {
    constructor(merchantId, order, amount, currency, cardNumber, cardHolderName, ccv, expiryMonth, expiryYear, hash = null) {
        this.merchantId = merchantId;
        this.order = order;
        this.amount = amount;
        this.currency = currency;
        this.card_number = cardNumber;
        this.card_holder_name = cardHolderName;
        this.expiry_month = expiryMonth;
        this.expiry_year = expiryYear;
        this.hash = hash;
        this.merchantRedirect = '';
        this.serviceName = 'customizableForm';
    }

    setHash(hash) {
        this.hash = hash; 
    }
}