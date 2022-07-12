const tokenizationSpecification = {
    type: 'PAYMENT_GATEWAY',
    parameters: {
        gateway : 'example',
        gatewayMerchantId: 'gatewayMerchantId'
    }
}

const cardPaymentMethod = {
    type: 'CARD',
    tokenizationSpecification: tokenizationSpecification,
    parameters:{
        allowedCardNetworks: ['VISA', 'MASTERCARD', 'AMEX'],
        allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS']
    }
}

const googlePayConfiguration = {
    apiVersion: 2,
    apiVersionMinor: 0,
    allowedPaymentMethods: [cardPaymentMethod]
}

let googlePayClient;

function onGooglePayLoaded(test){
    console.log(test)
    googlePayClient = new google.payments.api.PaymentsClient({
        environment: 'TEST'
    })

    googlePayClient.isReadyToPay(googlePayConfiguration)
        .then(response => {
            if (response.result) {
                createAndAddButton();
            }else{
                // asdas
            }
        })
        .catch(error => console.error('isReadyToPay error: ', error))
}

function createAndAddButton (){
    const googlePayButton = googlePayClient.createButton({
        onClick: onGooglePayButtonClicked,
    })

    $('.button-area .button-type').html(googlePayButton);
    // document.getElementById('buy-now').innerHTML = `${googlePayButton}`;
}

function onGooglePayButtonClicked () {
    const paymentDataRequest = { ...googlePayConfiguration };
    paymentDataRequest.merchantInfo = {
        merchantId: 'BCR2DN4TYDJNNUIC',
        merchantName: 'CcTest'
    }

    paymentDataRequest.transactionInfo = {
        totalPriceStatus: 'FINAL',
        totalPrice: '1.1',
        currencyCode: 'EUR',
        countryCode: 'ES',
    };

    googlePayClient.loadPaymentData(paymentDataRequest)
        .then(paymentData => proccessPaymentData(paymentData))
        .catch(error => console.error('loadPaymentData error:' , error))
}

function proccessPaymentData (paymentData) {
    fetch(ordersEndpontUrl, {
        headers: {
            'Content-Type' : 'application/json'
        },
        body: paymentData
    })
}
