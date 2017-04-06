<?php

// https://developer.paypal.com/webapps/developer/docs/api/#create-an-agreement

//create the plan
$createdPlan = require 'UpdatePlan.php';

//we can create the plans separately and store paypal ids in db

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

/* Create a new instance of Agreement object
{
    "name": "Base Agreement",
    "description": "Basic agreement",
    "start_date": "2015-06-17T9:45:04Z",
    "plan": {
      "id": "P-1WJ68935LL406420PUTENA2I"
    },
    "payer": {
      "payment_method": "paypal"
    },
    "shipping_address": {
        "line1": "111 First Street",
        "city": "Saratoga",
        "state": "CA",
        "postal_code": "95070",
        "country_code": "US"
    }
}*/
$agreement = new Agreement();

$agreement->setName('Membership type 1')
    ->setDescription('some description about the membership')
    ->setStartDate(date('Y-m-d').'T'.date('H:i:s').'Z');

// Add Plan ID
// Please note that the plan Id should be only set in this case.
$plan = new Plan();
$plan->setId($createdPlan->getId());
$agreement->setPlan($plan);

// Add Payer
$payer = new Payer();
$payer->setPaymentMethod('paypal');
$agreement->setPayer($payer);

// Add Shipping Address
$shippingAddress = new ShippingAddress();
$shippingAddress->setLine1('111 First Street')
    ->setCity('Saratoga')
    ->setState('CA')
    ->setPostalCode('95070')
    ->setCountryCode('US');
$agreement->setShippingAddress($shippingAddress);

// ### Create Agreement
try {
    // Please note that as the agreement has not yet activated, we wont be receiving the ID just yet.
    $agreement = $agreement->create($apiContext);
    $approvalUrl = $agreement->getApprovalLink();
} catch (Exception $ex) {
    //error with creating subscription payment, log to rollbar and notify via email
    echo "Error with creating subscription payment! Please contact our administrator";
    echo "<pre>";
    print_r($ex);
    echo "<pre>";
    die;
    exit(1);
}
echo "
    <script type='text/javascript'>window.location.href='{$approvalUrl}'</script>
    ";
return $agreement;
