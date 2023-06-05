<?php

namespace Tests\Unit;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PHPUnit\Framework\TestCase;
use Uctoplus\QrPaymentWrapper\CountriesEnum;
use Uctoplus\QrPaymentWrapper\QrPaymentRepository;

/**
 * Class GeneratorTest
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Tests\Unit
 */
class GeneratorTest extends TestCase
{
    /**
     * @dataProvider _ibans
     */
    public function test_create_qr_code($ibans, $bic, $country)
    {
        $repo = new QrPaymentRepository($ibans, $bic, $country);
        $repo->setBeneficiaryName("Mario Cechovic");
        $repo->setBeneficiaryAddress('Street 123', '12345', 'Some city - south', 'SK');
        $repo->setCreditorReference("12554");
        $repo->setDueDate(new \DateTime());
        $repo->setAmount(500);
        $repo->setCurrency('EUR');
        $repo->setPaymentReference('test payment');

        $repo->setDebtorName('Customer Name')
            ->setDebtorAddress('Near river st 1521/2', '54321', 'Small city', 'CZ');

        $qrString = $repo->getQrString();
        $this->assertNotEmpty($qrString);

        if ($country != CountriesEnum::HR && substr($ibans, 0, 2) == 'HR') {
            $renderer = new ImageRenderer(
                new RendererStyle(512),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $writer->writeFile($repo->getQrString(), $ibans . "-" . $country . ".png");

            $this->assertFileExists($ibans . "-" . $country . ".png");
        }
    }

    public function _ibans()
    {
        return [
            ["SK6511000000002618181236", "TATRSKBX", "SK"],
            ["SK6511000000002618181236", "TATRSKBX", "CZ"],
            ["SK6511000000002618181236", "TATRSKBX", "EU"],
            ["SK6511000000002618181236", "TATRSKBX", "CH"],

            ["CZ8850511124577476249167", "KOMBCZPP", "SK"],
            ["CZ8850511124577476249167", "KOMBCZPP", "CZ"],
            ["CZ8850511124577476249167", "KOMBCZPP", "EU"],
            ["CZ8850511124577476249167", "KOMBCZPP", "CH"],

            ["AT561936011357746782", "BMASAT21", "SK"],
            ["AT561936011357746782", "BMASAT21", "CZ"],
            ["AT561936011357746782", "BMASAT21", "EU"],
            ["AT561936011357746782", "BMASAT21", "CH"],

            ["CH4431999123000889012", "TATRSKBX", "CH"],

            ["HR5224840083236715977", "TATRSKBX", "HR"],
        ];
    }
}