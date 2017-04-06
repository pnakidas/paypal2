<?php

// # Update a plan
// https://developer.paypal.com/webapps/developer/docs/api/#update-a-plan
// API used:  /v1/payments/billing-plans/<Plan-Id>

//first create a plan and then set state from CREATED to ACTIVE
$createdPlan = require 'CreatePlan.php';

use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Plan;
use PayPal\Common\PayPalModel;

try {
    $patch = new Patch();

    $value = new PayPalModel('{
	       "state":"ACTIVE"
	     }');

    $patch->setOp('replace')
        ->setPath('/')
        ->setValue($value);
    $patchRequest = new PatchRequest();
    $patchRequest->addPatch($patch);

    $createdPlan->update($patchRequest, $apiContext);

    $plan = Plan::get($createdPlan->getId(), $apiContext);
} catch (Exception $ex) {
    //error updating plan, log to rollbar and notify via email
    echo "Error updating plan! Please contact our administrator";
    exit(1);
}

echo "Updated the Plan to Active State: ".$plan->getId();
return $plan;
