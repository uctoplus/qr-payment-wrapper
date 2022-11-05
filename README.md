# QR Payment Wrapper

For Czech, Slovak and European banks.

This is a wrapper that simplifies work with
Rikudou's packages for [Czech](https://github.com/RikudouSage/QrPaymentCZ), 
[Slovak](https://github.com/RikudouSage/QrPaymentSK)
and [European](https://github.com/RikudouSage/QrPaymentEU) QR payments.

Read the individual documentations of the libraries if you want to know more about them.

## Installation

Run `composer require uctoplus/qr-payment-wrapper`.

### Example

```php
<?php

$repo = new QrPaymentRepository($ibans, $bic, $country);
$repo->setBeneficiaryName("Mario Cechovic");
$repo->setCreditorReference("12554");

$qrString = $repo->getQrString();
$this->assertNotEmpty($qrString);
```
