<?php

namespace Uctoplus\QrPaymentWrapper\EU;

use rikudou\EuQrPayment\Iban\IBAN;
use rikudou\SkQrPayment\Iban\IbanBicPair;
use rikudou\SkQrPayment\QrPayment;
use Uctoplus\QrPaymentWrapper\Exceptions\QrParserException;

class QrPaymentParser
{

    public function parse($data)
    {
//        $exploded = preg_split('/\r\n|\r|\n/', $data);    //nejde..?
        $_data = str_replace(['\r', '\n'], ['\r\n', '\r\n'], $data);
        $exploded = explode('\r\n', $_data);

        $_version = $exploded[1];
        $_charSet = $exploded[2];
        $_type = $exploded[3];  //'SCT' => Sepa Credit Transfer
        $_bic = $exploded[4];
        $_beneficiaryName = $exploded[5];
        $_iban = $exploded[6];
        $_currencyAmount = $exploded[7];
        $_purpose = $exploded[8];
        $_referenceInv = $exploded[9];
        $_referenceText = $exploded[10];

        if (array_key_exists(11, $exploded))
            $_info = $exploded[11];

//        $iban = new IbanBicPair($_iban, $_bic);
//        $payment->addIban($iban);
        $iban = new IBAN($_iban);
        $payment = new \rikudou\EuQrPayment\QrPayment($iban);

        if (!empty($_beneficiaryName))
            $payment->setBeneficiaryName($_beneficiaryName);

//        preg_match_all('/\((\d+(?:\.\d+)?)\)/', $_currencyAmount, $amount);
        $currency = substr($_currencyAmount, 0, 3);
        $amount = substr($_currencyAmount, 3);

        $payment->setCurrency($currency);
        $payment->setAmount($amount);

        $payment->setComment($_referenceInv . $_referenceText);

//        dump($exploded, $payment);

        return $payment;
    }
}