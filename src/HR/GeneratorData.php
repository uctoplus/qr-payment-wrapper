<?php

namespace Uctoplus\QrPaymentWrapper\HR;

class GeneratorData
{
    public $payer, $payee;
    public $iban, $currency, $amount, $model, $reference, $code, $description;

    public function __construct(
        $payer,
        $payee,
        $iban,
        $currency,
        $amount,
        $model,
        $reference,
        $code,
        $description
    )
    {
        if (!verify_iban($iban, true))
            throw new \UnexpectedValueException('invalid IBAN provided');

        if (strlen($currency) !== 3)
            throw new \UnexpectedValueException('invalid currency');

        if (strlen($model) !== 4)
            throw new \UnexpectedValueException('invalid model');

        if (strlen($reference) > 22)
            throw new \UnexpectedValueException('reference too long');

        if (strlen($code) !== 4)
            throw new \UnexpectedValueException('invalid code');

        $this->payer = $payer;
        $this->payee = $payee;
        $this->iban = $iban;
        $this->currency = $currency;
        $this->amount = $amount * 100;
        $this->model = $model;
        $this->reference = $reference;
        $this->code = $code;
        $this->description = $description;
    }
}