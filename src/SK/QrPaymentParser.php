<?php

namespace Uctoplus\QrPaymentWrapper\SK;

use DateTime;
use rikudou\SkQrPayment\Iban\IbanBicPair;
use rikudou\SkQrPayment\QrPayment;
use Uctoplus\QrPaymentWrapper\Exceptions\QrParserException;
use Uctoplus\QrPaymentWrapper\Interfaces\QrParserInterface;

/**
 * Class QrPaymentParser
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\SK
 */
class QrPaymentParser extends BaseQrParser implements QrParserInterface
{
    /**
     * @param $data
     * @return QrPayment
     * @throws QrParserException
     */
    public function parse($data)
    {
        $step1 = $this->convertToBytes($data);
        $step2 = $this->decompress($step1);

        $explode = explode("\t", $step2);

        $payment = new QrPayment();
        $payment->setInternalId($explode[0]);

        $payment->setAmount($explode[3]);
        $payment->setCurrency($explode[4]);
        if (!empty($explode[5])) {
            $payment->setDueDate(DateTime::createFromFormat("Ymd", $explode[5]));
        }
        $payment->setVariableSymbol($explode[6]);
        $payment->setConstantSymbol($explode[7]);
        $payment->setSpecificSymbol($explode[8]);
        if (!empty($explode[9])) {
            var_dump($explode[9]);
        }
        $payment->setComment($explode[10]);
        $ibans_count = $explode[11];

        $pointer = 12;
        for ($i = 0; $i < $ibans_count; $i++) {
            $iban = new IbanBicPair($explode[$pointer], $explode[$pointer + 1]);
            $payment->addIban($iban);
            $pointer += 2;
        }

        if (isset($explode[$pointer + 1]))
            $payment->setPayeeName($explode[$pointer + 1]);

        if (isset($explode[$pointer + 2]))
            $payment->setPayeeAddressLine1($explode[$pointer + 2]);

        if (isset($explode[$pointer + 3]))
            $payment->setPayeeAddressLine2($explode[$pointer + 3]);

        return $payment;
    }
}