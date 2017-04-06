<?php

//Create Sample Plan
// https://developer.paypal.com/webapps/developer/docs/api/#create-a-plan

require __DIR__ . '/bootstrap.php';
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;

//create plan object and set details
$plan = new Plan();

$plan->setName('Test Membership1')
    ->setDescription('Test membership description')
    ->setType('fixed');

//create plan definintions and set payment attributes (like frequenecy, value etc)
$paymentDefinition = new PaymentDefinition();
$paymentDefinition->setName('Regular Payments')
    ->setType('REGULAR')
    ->setFrequency('Month')
    ->setFrequencyInterval("1")
    ->setCycles("12")
    ->setAmount(new Currency(array('value' => 99.95, 'currency' => 'USD')));

//create any extras on payment (like tax, shipping etc) - probably wont need in our case
$chargeModel = new ChargeModel();
$chargeModel->setType('SHIPPING')
    ->setAmount(new Currency(array('value' => 10, 'currency' => 'USD')));

$paymentDefinition->setChargeModels(array($chargeModel));

//create merchant preferences like redirect urls/setup charges etc
$merchantPreferences = new MerchantPreferences();
$baseUrl = getBaseUrl();

$merchantPreferences->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")
    ->setCancelUrl("$baseUrl/ExecutePayment.php?success=false")
    ->setAutoBillAmount("yes")
    ->setInitialFailAmountAction("CONTINUE")
    ->setMaxFailAttempts("0");


$plan->setPaymentDefinitions(array($paymentDefinition));
$plan->setMerchantPreferences($merchantPreferences);

try {
    $output = $plan->create($apiContext);
} catch (Exception $ex) {
    //error creating plan, log to rollbar and notify via email
    echo "Error creating plan! Please contact our administrator";
    exit(1);
}

// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
echo "Created Plan: ". $output->getId();
return $output;
