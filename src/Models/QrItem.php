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
     * @var int
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
     * @return int
     */
    public function getBaseAmount(): int
    {
        return $this->baseAmount;
    }

    /**
     * @param int $baseAmount
     * @return QrItem
     */
    public function setBaseAmount(int $baseAmount): self
    {
        $this->baseAmount = $baseAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getVatAmount(): int
    {
        return $this->vatAmount;
    }

    /**
     * @param int $vatAmount
     * @return QrItem
     */
    public function setVatAmount(int $vatAmount): self
    {
        $this->vatAmount = $vatAmount;
        return $this;
    }

    /**
     * @return int
     */
    public function getVatRate(): int
    {
        return $this->vatRate;
    }

    /**
     * @param int $vatRate
     * @return QrItem
     */
    public function setVatRate(int $vatRate): self
    {
        $this->vatRate = $vatRate;
        return $this;
    }


}