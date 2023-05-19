<?php

namespace Uctoplus\QrPaymentWrapper\CZ;

use rikudou\SkQrPayment\Iban\IbanBicPair;

class QrPaymentParser
{
    public function parse($data)
    {
        $exploded = explode('*', $data);

        $_accSum = str_replace('ACC:', '', $exploded[2]);
        $_ibanExploded = explode('+', $_accSum);

        $iban = new IbanBicPair($_ibanExploded[0], $_ibanExploded[1]);

        $payment = new \Rikudou\CzQrPayment\QrPayment($iban);
        $payment->setAmount(str_replace('AM:', '', $exploded[3]));
        $payment->setCurrency(str_replace('CC:', '', $exploded[4]));
        $payment->setComment(str_replace('MSG:', '', $exploded[5]));
        $payment->setDueDate(\DateTime::createFromFormat("Ymd", str_replace('DT:', '', $exploded[6])));
        $payment->setVariableSymbol(str_replace('CC:', '', $exploded[7]));
        $payment->setConstantSymbol(str_replace('CC:', '', $exploded[8]));

        return $payment;
    }
}