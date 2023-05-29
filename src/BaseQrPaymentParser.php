<?php

namespace Uctoplus\QrPaymentWrapper;

use rikudou\SkQrPayment\QrPayment;
use Uctoplus\QrPaymentWrapper\Exceptions\QrParserException;

class BaseQrPaymentParser
{
    private $_parsers = [
        \Uctoplus\QrPaymentWrapper\CZ\QrPaymentParser::class,
        \Uctoplus\QrPaymentWrapper\EU\QrPaymentParser::class,
        \Uctoplus\QrPaymentWrapper\SK\QrPaymentParser::class,
    ];

    private $parser;

    /**
     * @param $data
     * @return QrPayment
     * @throws QrParserException
     */
    public function parse($data)
    {
        foreach ($this->_parsers as $parserClass) {
            try {
                $parser = new $parserClass;
                $res = $parser->parse($data);

                $this->parser = $parser;

                return $res;
            } catch (\Throwable $throwable) {
                continue;
            }
        }
    }

    /**
     * @return mixed
     */
    public function getParser()
    {
        return $this->parser;
    }
}