<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Cart_Payment_Strategy_Wirecard implements Moraso_Cart_Payment_Strategy {

    public function getCheckoutUrl() {

        return 'https://secure.wirecard-cee.com/qpay/init.php';
    }

    public function getHiddenFormFields() {

        $cartData = Moraso_Cart::getData($cart_id);

        $currency = Moraso_Config::get('moraso.shop.currency');
        $language = Moraso_Config::get('moraso.shop.language');
        $imageURL = Moraso_Config::get('moraso.shop.imageURL');

        $cancelURL = Moraso_Config::get('moraso.shop.checkout.cancelURL');
        $failureURL = Moraso_Config::get('moraso.shop.checkout.failureURL');
        $pendingURL = Moraso_Config::get('moraso.shop.checkout.pendingURL');
        $successURL = Moraso_Config::get('moraso.shop.checkout.successURL');
        $confirmURL = Moraso_Config::get('moraso.shop.checkout.confirmURL');
        $serviceURL = Moraso_Config::get('moraso.shop.checkout.serviceURL');

        $orderDescription = Moraso_Config::get('moraso.shop.checkout.orderDescription');
        $displayText = Moraso_Config::get('moraso.shop.checkout.displayText');

        $customerId = Moraso_Config::get('moraso.shop.payment.wirecard.customerId');

        $hiddenFormFields = array(
            'customerId' => $customerId,
            'amount' => $cartData['amount'],
            'currency' => $currency,
            'language' => $language,
            'orderDescription' => $orderDescription,
            'successURL' => Moraso_Config::get('sys.webpath') . $successURL,
            'confirmURL' => Moraso_Config::get('sys.webpath') . $confirmURL,
            'order_id' => $cart_id
        );

        $requestFingerprint = $this->_generateRequestFingerprint($hiddenFormFields);

        $hiddenFormFields['paymenttype'] = 'SELECT';
        $hiddenFormFields['displayText'] = $displayText;
        $hiddenFormFields['cancelURL'] = Moraso_Config::get('sys.webpath') . $cancelURL;
        $hiddenFormFields['failureURL'] = Moraso_Config::get('sys.webpath') . $failureURL;
        $hiddenFormFields['pendingURL'] = Moraso_Config::get('sys.webpath') . $pendingURL;
        $hiddenFormFields['imageURL'] = $imageURL;
        $hiddenFormFields['serviceURL'] = Moraso_Config::get('sys.webpath') . $serviceURL;
        $hiddenFormFields['requestFingerprintOrder'] = $requestFingerprint['requestFingerprintOrder'];
        $hiddenFormFields['requestfingerprint'] = $requestFingerprint['requestFingerprint'];

        return $hiddenFormFields;
    }

    private function _generateRequestFingerprint(array $data) {

        $requestFingerprintOrder = array();
        $requestFingerprintSeed = array();

        $requestFingerprintOrder[] = 'secret';
        $requestFingerprintSeed[] = Moraso_Config::get('moraso.shop.payment.wirecard.secret');

        foreach ($data as $key => $value) {
            $requestFingerprintOrder[] = $key;
            $requestFingerprintSeed[] = $value;
        }

        $requestFingerprintOrder[] = "requestFingerprintOrder";
        $requestFingerprintSeed[] = implode(',', $requestFingerprintOrder);

        return array(
            'requestFingerprintOrder' => implode(',', $requestFingerprintOrder),
            'requestFingerprint' => md5(implode('', $requestFingerprintSeed))
        );
    }

    public function doConfirmPayment() {

        return false;
    }

}