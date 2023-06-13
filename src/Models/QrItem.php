<?php

namespace Uctoplus\QrPaymentWrapper\Models;

class QrItem
{
    /**
     * @var string
     */
    protected $title = "";

    /**
     * @var float
     */
    protected $baseAmount = 0;

    /**
     * @var float
     */
    protected $vatAmount = 0;

    /**
     * @var float
     */
    protected $vatRate = 0;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return QrItem
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return float|int
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @param $baseAmount
     * @return QrItem
     */
    public function setBaseAmount($baseAmount): self
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

    /**
     * @return float|int
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param $vatAmount
     * @return QrItem
     */
    public function setVatAmount($vatAmount): self
    {
        $this->vatAmount = $vatAmount;
        return $this;
    }

    /**
     * @return float
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * @param $vatRate
     * @return $this
     */
    public function setVatRate($vatRate): self
    {
        $this->vatRate = $vatRate;
        return $this;
    }


}