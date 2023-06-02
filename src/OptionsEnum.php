<?php

namespace Uctoplus\QrPaymentWrapper;

class OptionsEnum
{
    const BENEFICIARY_NAME = "beneficiaryName";
    const BENEFICIARY_STREET_AND_NUMBER = "beneficiaryStreetAndNumber";
    const BENEFICIARY_ZIPCODE = "beneficiaryZipCode";
    const BENEFICIARY_CITY = "beneficiaryCity";
    const BENEFICIARY_COUNTRY_CODE2 = "beneficiaryCountryCode2";

    const CREDITOR_REFERENCE = "creditorReference";

    const DEBTOR_NAME = "creditorName";
    const DEBTOR_STREET_AND_NUMBER = "creditorStreetAndNumber";
    const DEBTOR_ZIPCODE = "creditorZipcode";
    const DEBTOR_CITY = "creditorCity";
    const DEBTOR_COUNTRY_CODE2 = "creditorCountryCode2";

    const REMITTANCE_TEXT = "remittanceText";
    const PAYMENT_REFERENCE = "paymentReference";
}