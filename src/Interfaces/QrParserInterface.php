<?php

namespace Uctoplus\QrPaymentWrapper\Interfaces;

/**
 * Interface QrParserInterface
 *
 * @author MimoGraphix <mimographix@gmail.com>
 * @copyright EpicFail | Studio
 * @package Uctoplus\QrPaymentWrapper\Interfaces
 */
interface QrParserInterface
{
    public function parse($data);
}