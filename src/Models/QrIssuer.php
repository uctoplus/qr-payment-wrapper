<?php

namespace Uctoplus\QrPaymentWrapper\Models;

/**
 * Class Issuer
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\Models
 */
class QrIssuer
{
    /**
     * @var string
     */
    protected $name = "";

    /**
     * @var string
     */
    protected $street = "";

    /**
     * @var string
     */
    protected $city = "";

    /**
     * @var string
     */
    protected $zip = "";

    /**
     * @var string
     */
    protected $country = "SVK";

    /**
     * @var string
     */
    protected $businessId = "";

    /**
     * @var string
     */
    protected $taxId = "";

    /**
     * @var string
     */
    protected $vatNumber = "";

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return QrIssuer
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return QrIssuer
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return QrIssuer
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     * @return QrIssuer
     */
    public function setZip(string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return QrIssuer
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessId(): string
    {
        return $this->businessId;
    }

    /**
     * @param string $businessId
     * @return QrIssuer
     */
    public function setBusinessId(string $businessId): self
    {
        $this->businessId = $businessId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxId(): string
    {
        return $this->taxId;
    }

    /**
     * @param string $taxId
     * @return QrIssuer
     */
    public function setTaxId(string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    /**
     * @return string
     */
    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     * @return QrIssuer
     */
    public function setVatNumber(string $vatNumber): self
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }


}