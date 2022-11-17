<?php

namespace Tests\Unit;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PHPUnit\Framework\TestCase;
use Uctoplus\QrPaymentWrapper\QrPaymentRepository;

class GeneratorTest extends TestCase
{
    /**
     * @dataProvider _ibans
     */
    public function test_create_qr_code($ibans, $bic, $country)
    {
        $repo = new QrPaymentRepository($ibans, $bic, $country);
        $repo->setBeneficiaryName("Mario Cechovic");
        $repo->setCreditorReference("12554");
        $repo->setDueDate(new \DateTime());

        $qrString = $repo->getQrString();
        $this->assertNotEmpty($qrString);

        $renderer = new ImageRenderer(
            new RendererStyle(512),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $writer->writeFile($repo->getQrString(), $ibans . "-" . $country . ".png");

        $this->assertFileExists($ibans . "-" . $country . ".png");
    }

    public function _ibans()
    {
        return [
            ["SK6511000000002618181236", "TATRSKBX", "SK"],
            ["SK6511000000002618181236", "TATRSKBX", "CZ"],
            ["SK6511000000002618181236", "TATRSKBX", "EU"],

            ["CZ8850511124577476249167", "KOMBCZPP", "SK"],
            ["CZ8850511124577476249167", "KOMBCZPP", "CZ"],
            ["CZ8850511124577476249167", "KOMBCZPP", "EU"],

            ["AT561936011357746782", "BMASAT21", "SK"],
            ["AT561936011357746782", "BMASAT21", "CZ"],
            ["AT561936011357746782", "BMASAT21", "EU"],
        ];
    }
}