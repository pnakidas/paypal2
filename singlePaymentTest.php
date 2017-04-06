<?php

//create single payment using paypal

require __DIR__ . '/bootstrap.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

//create a payer and set payment method to paypal
$payer = new Payer();
$payer->setPaymentMethod("paypal");

//create line items for invoice
$item1 = new Item();
$item1->setName('Item 1')
    ->setCurrency('USD')
    ->setQuantity(1)
    ->setSku(mt_rand(10000, 99999))
    ->setPrice(7.5);

$qty2 = mt_rand(1, 10);
$item2 = new Item();
$item2->setName('Item 2')
    ->setCurrency('USD')
    ->setQuantity($qty2)
    ->setSku(mt_rand(10000, 99999))
    ->setPrice(2);

$itemList = new ItemList();
$itemList->setItems(array($item1, $item2));

//create any details on the payment - taxes, shipping etc
$shipping = 1.2;
$tax  = 1.3;
$subtotal = 7.5+$qty2*2;
$details = new Details();
$details->setShipping($shipping)
    ->setTax($tax)
    ->setSubtotal($subtotal);

//create a total amount object by combining the total and details
$amount = new Amount();
$amount->setCurrency("USD")
    ->setTotal($shipping + $tax + $subtotal)
    ->setDetails($details);

//create transaction object
$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setItemList($itemList)
    ->setDescription("Test Payment description")
    ->setInvoiceNumber(uniqid());

//create redirect url, will be  replaced by codeigniter
$baseUrl = getBaseUrl();
$redirectUrls = new RedirectUrls();
$redirectUrls->setReturnUrl("$baseUrl/ExecutePayment.php?success=true")
    ->setCancelUrl("$baseUrl/ExecutePayment.php?success=false");

//create the payment object and set urls
$payment = new Payment();
$payment->setIntent("sale")
    ->setPayer($payer)
    ->setRedirectUrls($redirectUrls)
    ->setTransactions(array($transaction));

//try payment
try {
    $payment->create($apiContext);
} catch (Exception $ex) {
    //error with payment, log to rollbar and notify via email
    echo "Error with payment! Please contact our administrator";
    exit(1);
}

// if payment is successfully created, get the approval url and redirect user here  for approving  the payment
$approvalUrl = $payment->getApprovalLink();

echo "
    <script type='text/javascript'>window.location.href='{$approvalUrl}'</script>
    ";