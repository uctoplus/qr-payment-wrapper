<?php

namespace Uctoplus\QrPaymentWrapper;

use Le\PDF417\Renderer\SvgRenderer;
use DateTimeInterface;
use Exception;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use InvalidArgumentException;
use LogicException;
use rikudou\EuQrPayment\Exceptions\UnsupportedMethodException;
use rikudou\EuQrPayment\Iban\IBAN;
use Rikudou\QrPayment\QrPaymentInterface;
use rikudou\SkQrPayment\Iban\IbanBicPair;
use rikudou\SkQrPayment\Payment\QrPaymentOptions;
use rikudou\SkQrPayment\QrPayment;
use Sprain\SwissQrBill\DataGroup\Element\CombinedAddress;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
use stdClass;
use Uctoplus\QrPaymentWrapper\HR\Generator;
use Uctoplus\QrPaymentWrapper\HR\GeneratorData;

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
            case (CountriesEnum::CH && in_array(substr($this->iban, 0, 2), ['CH', 'LI'])):
                $qrPayment = $this->chPayment();
                return $qrPayment->getQrCode()->getText();
//            case (CountriesEnum::HR && substr($this->iban, 0, 2) == 'HR'):
//                return $this->hrPayment();
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
            if (!method_exists($qrPaymentInterface, $methodName)) {
                continue;
            }
            try {
                call_user_func([$qrPaymentInterface, $methodName], $value);
                $options[$key] = $value;
            } catch (UnsupportedMethodException $e) {
                // skip if method unsupported
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

    public function setDebtorName(string $debtorName)
    {
        if ($this->country == CountriesEnum::HR)
            $this->checkLength($debtorName, 22);
        else
            $this->checkLength($debtorName, 70);

        $this->options[OptionsEnum::DEBTOR_NAME] = $debtorName;

        return $this;
    }

    public function setDebtorAddress(string $streetAndNumber, string $zipcode, string $city, string $countryCode2)
    {
        $this->checkLength($streetAndNumber, 70);
        $this->checkLength($zipcode, 10, 3);
        $this->checkLength($city, 70);
        $this->checkLength($countryCode2, 2, 2);

        $this->options[OptionsEnum::DEBTOR_STREET_AND_NUMBER] = $streetAndNumber;
        $this->options[OptionsEnum::DEBTOR_ZIPCODE] = $zipcode;
        $this->options[OptionsEnum::DEBTOR_CITY] = $city;
        $this->options[OptionsEnum::DEBTOR_COUNTRY_CODE2] = $countryCode2;

        return $this;
    }

    public function setBeneficiaryName(string $beneficiaryName)
    {
        $this->checkLength($beneficiaryName, 70);
        $this->options[OptionsEnum::BENEFICIARY_NAME] = $beneficiaryName;

        return $this;
    }

    public function setBeneficiaryAddress(string $streetAndNumber, string $zipcode, string $city, string $countryCode2)
    {
        $this->checkLength($streetAndNumber, 70);
        $this->checkLength($zipcode, 10, 3);
        $this->checkLength($city, 70);
        $this->checkLength($countryCode2, 2, 2);

        $this->options[OptionsEnum::BENEFICIARY_STREET_AND_NUMBER] = $streetAndNumber;
        $this->options[OptionsEnum::BENEFICIARY_ZIPCODE] = $zipcode;
        $this->options[OptionsEnum::BENEFICIARY_CITY] = $city;
        $this->options[OptionsEnum::BENEFICIARY_COUNTRY_CODE2] = $countryCode2;

        return $this;
    }

    public function setPaymentReference(string $reference)
    {
        if ($this->country == CountriesEnum::CH) {
            $reference = preg_replace("/[^0-9]/", "", $reference);
            $reference = str_pad($reference, 27, '0', STR_PAD_LEFT);
            $this->checkLength($reference, 27);
        } else if ($this->country == CountriesEnum::HR) {
            $this->checkLength($reference, 22);
        } else {
            $this->checkLength($reference, 40);
        }

        $this->options[OptionsEnum::PAYMENT_REFERENCE] = $reference;

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
            $iban = new IBAN($this->iban);
        } elseif (is_array($this->iban)) {
            $iban = new IBAN($this->iban[0]);
        } else {
            throw new Exception("IBAN not correct");
        }
        return new \rikudou\EuQrPayment\QrPayment($iban);
    }

    public function chPayment()
    {
        $qrBill = QrBill::create();

        $creditor = CombinedAddress::create(
            $this->options[OptionsEnum::BENEFICIARY_NAME],
            $this->options[OptionsEnum::BENEFICIARY_STREET_AND_NUMBER],
            $this->options[OptionsEnum::BENEFICIARY_ZIPCODE] . ' ' . $this->options[OptionsEnum::BENEFICIARY_CITY],
            $this->options[OptionsEnum::BENEFICIARY_COUNTRY_CODE2]
        );
        $qrBill->setCreditor($creditor);

        $creditorInormation = CreditorInformation::create($this->iban);
        $qrBill->setCreditorInformation($creditorInormation);

        $paymentAmountInfo = PaymentAmountInformation::create($this->options[QrPaymentOptions::CURRENCY], $this->options[QrPaymentOptions::AMOUNT]);
        $qrBill->setPaymentAmountInformation($paymentAmountInfo);

        $paymentReference = PaymentReference::create(PaymentReference::TYPE_QR, $this->options[OptionsEnum::PAYMENT_REFERENCE]);
        $qrBill->setPaymentReference($paymentReference);

        if (!empty($this->options[OptionsEnum::DEBTOR_NAME])) {
            $debtor = CombinedAddress::create(
                $this->options[OptionsEnum::DEBTOR_NAME],
                $this->options[OptionsEnum::DEBTOR_STREET_AND_NUMBER],
                $this->options[OptionsEnum::DEBTOR_ZIPCODE] . ' ' . $this->options[OptionsEnum::DEBTOR_CITY],
                $this->options[OptionsEnum::DEBTOR_COUNTRY_CODE2]
            );
            $qrBill->setUltimateDebtor($debtor);
        }

        return $qrBill;
    }

    protected function hrPayment()
    {
        if (empty($this->options[OptionsEnum::HR_MODEL])) {
            $this->options[OptionsEnum::HR_MODEL] = "HR00";
        }

//        if(empty($this->options[OptionsEnum::HR_PURPOSE])){
//            $this->options[OptionsEnum::HR_PURPOSE] = "COST";
//        }

        $debtor = new stdClass();
        $debtor->name = $this->options[OptionsEnum::DEBTOR_NAME] ?? '';
        $debtor->address = $this->options[OptionsEnum::DEBTOR_STREET_AND_NUMBER] ?? '';
        $debtor->city = $this->options[OptionsEnum::DEBTOR_CITY] ?? '';

        $creditor = new stdClass();
        $creditor->name = $this->options[OptionsEnum::BENEFICIARY_NAME] ?? '';
        $creditor->address = $this->options[OptionsEnum::BENEFICIARY_STREET_AND_NUMBER] ?? '';
        $creditor->city = $this->options[OptionsEnum::BENEFICIARY_CITY] ?? '';

        $data = new GeneratorData(
            $debtor,
            $creditor,
            $this->iban,
            $this->getCurrency(),
            $this->getAmount(),
            $this->options[OptionsEnum::HR_MODEL] ?? '',
            $this->options[OptionsEnum::PAYMENT_REFERENCE] ?? '',
            $this->options[OptionsEnum::HR_PURPOSE] ?? "",
            $this->options[OptionsEnum::HR_DESCRIPTION] ?? $this->options[OptionsEnum::PAYMENT_REFERENCE]
        );

        $generator = new Generator(null, new SvgRenderer([
            'color' => 'black',
            'scale' => 5,
        ]));

        $html = $generator->render($data);

        return $html;
//        $posB = strpos($html, '<svg', 0);
//        return substr($html, $posB);
    }

    /**
     * @param $type
     * @return string
     * @throws Exception
     */
    public function generateChPaymentSlip($type = 'html')
    {
        if ($type !== 'html')
            throw new Exception('Not implemented yet');

        $qrPayment = $this->chPayment();

        $output = new HtmlOutput($qrPayment, 'en');

        $html = $output->setPrintable(false)
            ->getPaymentPart();

        return $html;

        /*
        $fpdf = new \FPDF('P', 'mm', 'A4');
        $fpdf->addPage();

        $payStrip = new FpdfOutput($qrPayment, 'en', $fpdf);
        $payStrip->setPrintable(false)
            ->getPaymentPart();

        $fname = 'test_pdf.pdf';
        $fpdf->Output($fname);
        */
    }

    /**
     * @param Imagick $image
     * @param $size
     * @param $angle
     * @param $x
     * @param $y
     * @param $color
     * @param $font
     * @param $text
     * @param $spacing
     * @return void
     */
    private function imagettftextWsb(&$image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0)
    {
        $ctx = new ImagickDraw();
        $ctx->setFillColor($color);
        $ctx->setFontSize($size);
        $ctx->setFont($font);

        if ($spacing == 0) {
            $image->annotateImage($ctx, $x, $y, $angle, $text);
        } else {
            $temp_x = $x;
            $iMax = strlen($text);
            for ($i = 0; $i < $iMax; $i++) {
                $image->annotateImage($ctx, $temp_x, $y, $angle, $text[$i]);
                $temp_x += $spacing + 14.7;
            }
        }
    }

    public function generateHrPaymentSlip($type = 'html')
    {
        $qrPayment = $this->hrPayment();    //svg

        $qrCode = new Imagick();
        $qrCode->readImageBlob($qrPayment);
//        $qrCode->setImageFormat('png32');
//        $png =  $imagick->getImageBlob();

        $font_roboto = __DIR__ . '/../resources/RobotoMono-Regular.ttf';

        $black = new ImagickPixel();
        $black->setColor('rgba(48, 48, 48, 1.0)');

        $slip = new Imagick();
        $slip->readImage(__DIR__ . '/../resources/hub-3a.jpg');

        $this->imagettftextWsb($slip, 18, 0, 402, 54, $black, $font_roboto, $this->getCurrency(), 3);

        $total = "=" . number_format($this->getAmount(), 2, "", "");
        $x_total = 768 - (strlen($total) * 17);
        $this->imagettftextWsb($slip, 18, 0, $x_total, 55, $black, $font_roboto, $total, 3);
        $this->imagettftextWsb($slip, 18, 0, 401, 160, $black, $font_roboto, $this->iban, 3);
        $this->imagettftextWsb($slip, 18, 0, 278, 202, $black, $font_roboto, $this->options[OptionsEnum::HR_MODEL], 3);
        $this->imagettftextWsb($slip, 18, 0, 384, 202, $black, $font_roboto, $this->options[OptionsEnum::PAYMENT_REFERENCE], 3);

        if (!empty($this->options[OptionsEnum::HR_PURPOSE])) {
            $purpose = $this->options[OptionsEnum::HR_PURPOSE];
            $this->imagettftextWsb($slip, 18, 0, 277, 250, $black, $font_roboto, $purpose, 3);
        }

        $this->imagettftextWsb($slip, 12, 0, 438, 227, $black, $font_roboto, $this->options[OptionsEnum::HR_DESCRIPTION] ?? $this->options[OptionsEnum::PAYMENT_REFERENCE]);
        $this->imagettftextWsb($slip, 12, 0, 805, 240, $black, $font_roboto, $this->options[OptionsEnum::HR_DESCRIPTION] ?? $this->options[OptionsEnum::PAYMENT_REFERENCE]);

        $total2 = $this->getCurrency() . " =" . number_format($this->getAmount(), 2, ",", "");
        $x_total2 = 1080 - (strlen($total2) * 7);
        $this->imagettftextWsb($slip, 12, 0, $x_total2, 54, $black, $font_roboto, $total2);

        $this->imagettftextWsb($slip, 14, 0, 35, 60, $black, $font_roboto, $this->options[OptionsEnum::DEBTOR_NAME] ?? '');
        $this->imagettftextWsb($slip, 14, 0, 35, 80, $black, $font_roboto, $this->options[OptionsEnum::DEBTOR_STREET_AND_NUMBER] ?? '');
        $this->imagettftextWsb($slip, 14, 0, 35, 100, $black, $font_roboto, ($this->options[OptionsEnum::DEBTOR_ZIPCODE] ?? '') . " " . ($this->options[OptionsEnum::DEBTOR_CITY] ?? ''));

        $this->imagettftextWsb($slip, 14, 0, 35, 200, $black, $font_roboto, $this->options[OptionsEnum::BENEFICIARY_NAME] ?? '');
        $this->imagettftextWsb($slip, 14, 0, 35, 220, $black, $font_roboto, $this->options[OptionsEnum::BENEFICIARY_STREET_AND_NUMBER] ?? '');
        $this->imagettftextWsb($slip, 14, 0, 35, 240, $black, $font_roboto, ($this->options[OptionsEnum::BENEFICIARY_ZIPCODE] ?? '') . " " . ($this->options[OptionsEnum::BENEFICIARY_CITY] ?? ''));

        $debtor_l = ($this->options[OptionsEnum::DEBTOR_NAME] ?? '') . ", " . ($this->options[OptionsEnum::DEBTOR_CITY] ?? '');
        $x_sender2 = 1080 - (strlen($debtor_l) * 7);
        $this->imagettftextWsb($slip, 12, 0, $x_sender2, 86, $black, $font_roboto, $debtor_l);


        $reference2 = $this->options[OptionsEnum::HR_MODEL] . " " . $this->options[OptionsEnum::PAYMENT_REFERENCE];
        $x_reference2 = 1080 - (strlen($reference2) * 7);
        $this->imagettftextWsb($slip, 12, 0, $x_reference2, 201, $black, $font_roboto, $reference2);

        $x_iban2 = 1080 - (strlen($this->iban) * 7);
        $this->imagettftextWsb($slip, 12, 0, $x_iban2, 162, $black, $font_roboto, $this->iban);


        $qrCode->resizeImage(280, 70, Imagick::FILTER_LANCZOS, 1, true);
        $slip->compositeImage($qrCode, Imagick::COMPOSITE_ATOP, 40, 305);

        $slip->setImageFormat('jpg');
        $png = $slip->getImageBlob();

        $htmlRes = '<div id="hr_payment_slip">'
            . '<img src="data:image/png;base64,' . base64_encode($png) . '" width="100%" />'
            . '</div>';

        file_put_contents('hr.html', $htmlRes);

        return $htmlRes;
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
                return $vs ?? "";
            case "SS":
                return $ss ?? "";
            case "KS":
                return $ks ?? "";
            default:
                return null;
        }
    }
}
