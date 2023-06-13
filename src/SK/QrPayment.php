<?php

namespace Uctoplus\QrPaymentWrapper\SK;

use DateTimeInterface;
use Rikudou\Iban\Iban\IbanInterface;
use rikudou\SkQrPayment\QrPayment as RikudouQrPayment;

class QrPayment
{
    /** @var RikudouQrPayment $__qrPayment */
    private $__qrPayment;

    /**
     * @var DateTimeInterface|null
     */
    private $dueDate = null;


    public function __construct(IbanInterface ...$ibans)
    {
        $this->__qrPayment = new RikudouQrPayment(...$ibans);
    }

    public function __get($property)
    {
        if (property_exists($this->__qrPayment, $property))
            return $this->__qrPayment->{$property};
    }

    public function __set($property, $value)
    {
        if (property_exists($this->__qrPayment, $property))
            return $this->__qrPayment->{$property} = $value;
    }

    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->__qrPayment, $method], $arguments);
    }

    public function setDueDate(?DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;
        $this->__qrPayment->setDueDate($dueDate);

        return $this;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }
}