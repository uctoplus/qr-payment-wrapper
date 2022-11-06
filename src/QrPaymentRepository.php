<?php

namespace Uctoplus\QrPaymentWrapper;

use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use LogicException;
use Rikudou\QrPayment\QrPaymentInterface;
use rikudou\SkQrPayment\Iban\IbanBicPair;
use rikudou\SkQrPayment\Payment\QrPaymentOptions;
use rikudou\SkQrPayment\QrPayment;

class QrPaymentRepository implements QrPaymentInterface
{
    protected $options = [];
    protected $iban;
    protected $bic;

    protected $xzBinary;

    public function __construct(string $iban, ?string $bic, $country = 'EU')
    {
        $this->iban = $iban;
        $this->bic = $bic;
        $this->country = $country;
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    public function setCurrency(string $currency)
    {
        $this->options[QrPaymentOptions::CURRENCY] = $currency;
        return $this;
    }

    public function setDueDate(DateTimeInterface $dueDate)
    {
        $this->options[QrPaymentOptions::DUE_DATE] = $dueDate;
        return $this;
    }

    public function setAmount(float $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('The amount cannot be less than 0');
        }
        if ($amount > 999999999.99) {
            throw new InvalidArgumentException('The maximum amount is 999,999,999.99');
        }

        $this->options[QrPaymentOptions::AMOUNT] = $amount;
        return $this;
    }

    public function getQrString(): string
    {
        $this->checkParameterValidity();

        switch ($this->country) {
            case CountriesEnum::SK:
                $qrPayment = $this->skPayment();
                break;
            case CountriesEnum::CZ:
                $qrPayment = $this->czPayment();
                break;
            default:
                $qrPayment = $this->euPayment();
        }

        $qrPayment->setOptions($this->filterValidOptions($this->options, $qrPayment));

        return $qrPayment->getQrString();
    }

    protected function checkParameterValidity(): void
    {
        if (empty($this->options[OptionsEnum::BENEFICIARY_NAME])) {
            throw new LogicException('The beneficiary name is a mandatory parameter');
        }
        if (!empty($this->options[OptionsEnum::CREDITOR_REFERENCE]) && !empty($this->options[OptionsEnum::REMITTANCE_TEXT])) {
            throw new LogicException('Only creditor reference or remittance text or neither can be set but not both');
        }
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    private function filterValidOptions(array $options, $qrPaymentInterface)
    {
        $_options = [];
        foreach ($options as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($qrPaymentInterface, $methodName)) {
                $_options[$key] = $value;
            }
        }

        return $_options;
    }

    public function getCurrency(): string
    {
        return $this->options[QrPaymentOptions::CURRENCY];
    }

    public function getAmount(): float
    {
        return $this->options[QrPaymentOptions::AMOUNT];
    }

    public function getDueDate(): DateTimeInterface
    {
        return $this->options[QrPaymentOptions::DUE_DATE];
    }

    public function setCreditorReference(string $creditorReference)
    {
        $this->options[OptionsEnum::CREDITOR_REFERENCE] = $creditorReference;

        return $this;
    }

    public function setBeneficiaryName(string $beneficiaryName)
    {
        $this->checkLength($beneficiaryName, 70);
        $this->options[OptionsEnum::BENEFICIARY_NAME] = $beneficiaryName;

        return $this;
    }

    /**
     * @param string $string
     * @param int $max
     * @param int $min
     */
    private function checkLength(string $string, int $max, int $min = 1): void
    {
        $length = mb_strlen($string);
        if ($length > $max || $length < $min) {
            throw new InvalidArgumentException("The string should be between {$min} and {$max} characters long, your string contains {$length} characters");
        }
    }

    protected function skPayment()
    {
        $iban = new IbanBicPair($this->iban, $this->bic);
        $qrPayment = new QrPayment($iban);
        if ($this->xzBinary != null) {
            $qrPayment->setXzBinary($this->xzBinary);
        }

        if (!empty($this->options[OptionsEnum::CREDITOR_REFERENCE])) {
            $qrPayment->setVariableSymbol($this->getFromE2E('VS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
            $qrPayment->setSpecificSymbol($this->getFromE2E('SS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
            $qrPayment->setConstantSymbol($this->getFromE2E('KS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
        }

        return $qrPayment;
    }

    protected function czPayment()
    {
        $iban = new IbanBicPair($this->iban, $this->bic);
        $qrPayment = new \rikudou\CzQrPayment\QrPayment($iban);

        if (!empty($this->options[OptionsEnum::CREDITOR_REFERENCE])) {
            $qrPayment->setVariableSymbol($this->getFromE2E('VS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
            $qrPayment->setSpecificSymbol($this->getFromE2E('SS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
            $qrPayment->setConstantSymbol($this->getFromE2E('KS', $this->options[OptionsEnum::CREDITOR_REFERENCE]));
        }

        return $qrPayment;
    }

    protected function euPayment()
    {
        if (is_string($this->iban)) {
            $iban = new \rikudou\EuQrPayment\Iban\IBAN($this->iban);
        } elseif (is_array($this->iban)) {
            $iban = new \rikudou\EuQrPayment\Iban\IBAN($this->iban[0]);
        } else {
            throw new Exception("IBAN not correct");
        }
        return new \rikudou\EuQrPayment\QrPayment($iban);
    }

    public function setXzBinary($path)
    {
        $this->xzBinary = $path;
        return $this;
    }

    private function getFromE2E(string $string, $e2e_reference)
    {
        $e2e_reference = explode('/', $e2e_reference);
        if (count($e2e_reference) == 4) {
            $vs = preg_replace('/\D/', '', $e2e_reference[1]);
            $ss = preg_replace('/\D/', '', $e2e_reference[2]);
            $ks = preg_replace('/\D/', '', $e2e_reference[3]);
        } elseif (count($e2e_reference) == 7) {
            $vs = preg_replace('/\D/', '', $e2e_reference[2]);
            $ss = preg_replace('/\D/', '', $e2e_reference[4]);
            $ks = preg_replace('/\D/', '', $e2e_reference[6]);
        }

        switch ($string) {
            case "VS":
                return $vs;
            case "SS":
                return $ss;
            case "KS":
                return $ks;
            default:
                return null;
        }
    }
}
