<?php

namespace Unit;

use PHPUnit\Framework\TestCase;
use Uctoplus\QrPaymentWrapper\SK\QrInvoiceParser;
use Uctoplus\QrPaymentWrapper\SK\QrPaymentParser;

/**
 * Class QrPaymentParserTest
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Unit
 */
class QrPaymentParserTest extends TestCase
{
    /**
     * @dataProvider _stringData
     */
    public function test_qr_doce_data_parse($data){

        $parser = new QrPaymentParser();
        $payment = $parser->parse($data);

        $this->assertNotNull($payment);

    }

    public function _stringData()
    {
        return [
            ["00062000FCD9L87G1IO6GUIDVT1BS09MU0DGU9G2M2JF92DPL8BB0R8OH5PNPMCHCGM6HJGNKISC70E60NG0Q958FU97P8OH0ORFDBABCVG6U3RG3JC6KPE79GIJMNVVNPT4000"],
            ["000780000A9813CS43S5HPE89GBVOSLM1KVB17T3EPSHRG8VP25TNBR1A132686B3R3CVSKPL4SAG544NVH160BJB5SANCDE8VTS97TEGF9DFTMA5LCCD6K2E9NHUINRK1U6GSTOIRTM52EVRMIEH7A9QT34RK2T00"],
            ["000AG000BCT3O9TI41J7Q9S9VNBSTC1OB9RVB138CFILDP3RO1RB2T528HDBOCFQU15DQM13KC07DF5C60VU0LAAOVI54GJPHCIH8SO65A77ATTRMFQ6PJ9IO9SUJH27DKGKECNF1NBNSK7IN7TAV4LHEP5ULFTSAP5TAJU2P62HO5J9SC0KS8T67D9E7BETUM9JVGNBJT244HJFJUNC0L4VIDPPC0FV9K2HC00"],
        ];
    }
}