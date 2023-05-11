<?php

namespace Uctoplus\QrPaymentWrapper\SK;

use Rikudou\Iban\Iban\IbanInterface;
use rikudou\SkQrPayment\Xz\XzBinaryLocator;
use rikudou\SkQrPayment\Xz\XzBinaryLocatorInterface;
use Uctoplus\QrPaymentWrapper\Exceptions\QrParserException;

/**
 * Class BaseQrParser
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\SK
 */
abstract class BaseQrParser
{
    /**
     * @var XzBinaryLocatorInterface
     */
    private $xzBinaryLocator;

    /**
     * @param IbanInterface ...$ibans
     */
    public function __construct(IbanInterface ...$ibans)
    {
        $this->xzBinaryLocator = new XzBinaryLocator(null);
    }

    protected function convertToBytes($data)
    {
        $binData = "";
        $split = str_split($data);
        foreach ($split as $i => $char) {
            $position = strpos("0123456789ABCDEFGHIJKLMNOPQRSTUV", $char);
            if (FALSE === $position) {
                $charCode = "0x" . bin2hex($char);
                throw new QrParserException("Invalid input char [" . $charCode . "] at index[" . $i . "]");
            }
            $binData .= str_pad(decbin($position), 5, "0", STR_PAD_LEFT);
        }

        $binLen = strlen($binData);
        $hexLen = floor($binLen / 8) * 2;
        $hexData = str_repeat("_", $hexLen);
        for ($i = 0; $i < $hexLen; $i++) {
            $hexData[$i] = base_convert(substr($binData, $i * 4, 4), 2, 16);
        }

        return hex2bin($hexData);
    }

    protected function decompress($compressedData)
    {
        $version = substr($compressedData, 0, 2);
        $CRC32 = substr($compressedData, 2, 2);
        $body = substr($compressedData, 4);
        $xzBinary = $this->getXzBinary();

        $xzProcess = proc_open("${xzBinary} --decompress -F raw --lzma1=lc=3,lp=0,pb=2,dict=128KiB -c -", [
            0 => [
                'pipe',
                'r',
            ],
            1 => [
                'pipe',
                'w',
            ],
        ], $xzProcessPipes);
        assert(is_resource($xzProcess));

        fwrite($xzProcessPipes[0], $body);
        fclose($xzProcessPipes[0]);

        $pipeOutput = stream_get_contents($xzProcessPipes[1]);
        fclose($xzProcessPipes[1]);
        proc_close($xzProcess);

        return $pipeOutput;
    }


    public function getXzBinaryLocator(): XzBinaryLocatorInterface
    {
        return $this->xzBinaryLocator;
    }

    public function setXzBinaryLocator(XzBinaryLocatorInterface $xzBinaryLocator): self
    {
        $this->xzBinaryLocator = $xzBinaryLocator;

        return $this;
    }

    public function setXzBinary(?string $binaryPath): self
    {
        $this->xzBinaryLocator = new XzBinaryLocator($binaryPath);

        return $this;
    }

    public function getXzBinary(): string
    {
        return $this->xzBinaryLocator->getXzBinary();
    }

}