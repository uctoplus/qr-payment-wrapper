<?php

namespace Uctoplus\QrPaymentWrapper\SK;

use Uctoplus\QrPaymentWrapper\Interfaces\QrParserInterface;
use Uctoplus\QrPaymentWrapper\Models\QrInvoice;
use Uctoplus\QrPaymentWrapper\Models\QrIssuer;
use Uctoplus\QrPaymentWrapper\Models\QrItem;

/**
 * Class QrInvoiceParser
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\SK
 */
class QrInvoiceParser extends BaseQrParser implements QrParserInterface
{

    /**
     * @param $data
     * @return \Uctoplus\QrPaymentWrapper\Models\QrInvoice
     */
    public function parse($data)
    {
        $step1 = $this->convertToBytes($data);
        $step2 = $this->decompress($step1);

        $explode = explode("\t", $step2);

        $invoice = new QrInvoice();
        $invoice->setIssueDate(\DateTime::createFromFormat("Ymd", $explode[1]));
        $invoice->setDeliveryDate(\DateTime::createFromFormat("Ymd", $explode[2]));
        $invoice->setCurrency($explode[5]);

        $issuer = new QrIssuer();
        $issuer->setName($explode[9]);
        $issuer->setTaxId($explode[10]);
        $issuer->setVatNumber($explode[11]);
        $issuer->setBusinessId($explode[12]);
        $issuer->setStreet($explode[13] . " " . $explode[14]);
        $issuer->setCity($explode[15]);
        $issuer->setZip($explode[16]);
        $issuer->setCountry($explode[18]);
        $invoice->setIssuer($issuer);

        $item = new QrItem();
        $item->setTitle($explode[31]);
        $item->setBaseAmount($explode[38]);
        $item->setVatAmount($explode[39]);
        $item->setVatRate($explode[37] * 100);
        $invoice->addItem($item);

        return $invoice;
    }
}