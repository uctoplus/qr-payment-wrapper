<?php

namespace Uctoplus\QrPaymentWrapper\CH;

use Exception;
use rikudou\EuQrPayment\Iban\IBAN;
use rikudou\EuQrPayment\QrPayment;

class QrPaymentParser
{
    public function parse($data)
    {
        $_data = str_replace(['\r', '\n'], ['\r\n', '\r\n'], $data);
        $exploded = explode('\r\n', $_data);

        if ($exploded[0] != 'SPC')
            throw new Exception('Format not valid.');

        $_version = $exploded[1];
        $_charSet = $exploded[2];
        $_iban = $exploded[3];
        $addressType = $exploded[4];   //'S' (structured) | 'K'  (combined)
        $_creditorName = $exploded[5];

        if ($addressType == 'S') {
            $_creditorStreet = $exploded[6];
            $_creditorStreetNumber = $exploded[7];
            $_creditorZipCode = $exploded[8];
            $_creditorCity = $exploded[9];
            $_creditorCountryCode = $exploded[10];
            $pointer = 10;
        } else if ($addressType == 'K') {
            $_creditorStreetAndHouseNumber = $exploded[6];
            $_creditorZipCodeAndCity = $exploded[7];
            $_creditorCountryCode = $exploded[8];
            $pointer = 8;
        }

        //teraz je tu "niekoľko" prázdnych riadkov (7 ideálne, ale môžu tu byť +2 kvôli tej 'K'-štruktúre)
        //..takže to jebem a idem, až kým nenájdem hodnotu, to je už uma
        $pointer++;
        while (empty(($_amount = $exploded[$pointer]))) {
            $pointer++;
        }

        $iban = new IBAN($_iban);
        $payment = new QrPayment($iban);


        if (!empty($_creditorName))
            $payment->setBeneficiaryName($_creditorName);

        $payment->setAmount($_amount);

        $pointer++;
        $payment->setCurrency($exploded[$pointer]);

        //dáta odosielateľa celkom ignoujem, lebo je to paltba a moc ma to netrápi -> štruktúra rovnaká ako vyššie, vrátane typu (S/K)
        //

        //..neviem kľko riadkov, takže hľadám koniec
        $pointer++;
        while ($exploded[$pointer] != 'EPD') {      //..pointer je terz na poslednom radku ('EPD'
            $pointer++;
        }

        if (!empty($exploded[$pointer - 1]))        //ak je vyplnený komentár
            $payment->setComment($exploded[$pointer - 1]);

        return $payment;
    }
}