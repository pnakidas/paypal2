<?php

$success = $_REQUEST['success'];

if($success === "true") {
    echo "Payment completed successfully";
    $paymentId = $_REQUEST['paymentId'];
    $token = $_REQUEST['token'];
    $PayerID = $_REQUEST['PayerID'];
    
    //record details in db?
} else {
    echo "Payment failed";
    //log to rollbar and send details via mail
}
