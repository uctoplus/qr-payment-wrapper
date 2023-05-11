<?php

namespace Uctoplus\QrPaymentWrapper\Models;

/**
 * Class Invoice
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\Models
 */
class QrInvoice
{
    /**
     * @var \DateTime|null
     */
    protected ?\DateTime $issueDate = null;

    /**
     * @var \DateTime|null
     */
    protected ?\DateTime $deliveryDate = null;

    /**
     * @var string
     */
    protected $currency = "EUR";

    /**
     * @var QrIssuer|null
     */
    protected ?QrIssuer $issuer = null;

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @return \DateTime|null
     */
    public function getIssueDate(): ?\DateTime
    {
        return $this->issueDate;
    }

    /**
     * @param \DateTime|null $issueDate
     * @return $this
     */
    public function setIssueDate(?\DateTime $issueDate): self
    {
        $this->issueDate = $issueDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveryDate(): ?\DateTime
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime|null $deliveryDate
     * @return $this
     */
    public function setDeliveryDate(?\DateTime $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return QrIssuer|null
     */
    public function getIssuer(): ?QrIssuer
    {
        return $this->issuer;
    }

    /**
     * @param QrIssuer|null $issuer
     * @return $this
     */
    public function setIssuer(?QrIssuer $issuer): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addItem($item): self
    {
        $this->items[] = $item;

        return $this;
    }


}