<?php

namespace Uctoplus\QrPaymentWrapper\HR;

use Le\PDF417\PDF417;
use Le\PDF417\Renderer\SvgRenderer;

class Generator
{
    private $pdf417, $renderer;

    public function __construct($pdf417 = null, $svgRenderer = null)
    {
        if (empty($pdf417)) {
            $this->pdf417 = new PDF417();
            $this->pdf417->setSecurityLevel(4);
            $this->pdf417->setColumns(9);
        } else {
            $this->pdf417 = $pdf417;
        }

        if (empty($svgRenderer)) {
            $this->renderer = new SvgRenderer(['color' => 'black']);
        } else {
            $this->renderer = $svgRenderer;
        }
    }

    public function render(GeneratorData $data): string
    {
        $content = $this->generateContent($data);
        $encoded = $this->pdf417->encode($content);
        return $this->renderer->render($encoded);
    }

    private function generateContent(GeneratorData $data): string
    {
        $parts = [];
        $parts[] = 'HRVHUB30';

        $parts[] = strtoupper($data->currency);
        $parts[] = str_pad($data->amount, 15, '0', STR_PAD_LEFT);

        $parts[] = mb_strimwidth($data->payer->name, 0, 30);
        $parts[] = mb_strimwidth($data->payer->address, 0, 27);
        $parts[] = mb_strimwidth($data->payer->city, 0, 27);

        $parts[] = mb_strimwidth($data->payee->name, 0, 25);
        $parts[] = mb_strimwidth($data->payee->address, 0, 25);
        $parts[] = mb_strimwidth($data->payee->city, 0, 27);

        $parts[] = $data->iban;
        $parts[] = $data->model;
        $parts[] = $data->reference;

        $parts[] = strtoupper($data->code);
        $parts[] = mb_strimwidth($data->description, 0, 35);
        $parts[] = '';

        return implode("\n", $parts);
    }
}