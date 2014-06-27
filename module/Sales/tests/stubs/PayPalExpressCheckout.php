<?php

// Returns
$apiTransactionHistory = array(
  0 =>
  array(
    "timestamp" => "2013-08-09T21:06:54Z",
    "transaction_id" => "I-SRDP8SP46N4E",
    "status" => "Created"
  ),
  1 =>
  array(
    "timestamp" => "2013-08-09T21:07:12Z",
    "transaction_id" => "9L336084CD065741B",
    "status" => "Completed",
    "amount" => "29.95",
  ),
  2 =>
  array(
    "timestamp" => "2013-08-10T12:17:41Z",
    "transaction_id" => "1L95785624688460H",
    "status" => "Completed",
    "amount" => "29.95"
  ),
  3 =>
  array(
    "timestamp" => "2013-08-11T12:17:28Z",
    "transaction_id" => "2CS244277V620760X",
    "status" => "Completed",
    "amount" => "29.95"
  ),
  4 =>
  array( 
    "timestamp" => "2013-08-12T12:05:59Z",
    "transaction_id" => "97R28510PW025542N",
    "status" => "Completed",
    "amount" => "29.95"
  ),
  5 =>
  array(
    "timestamp" => "2013-08-13T12:08:17Z",
    "transaction_id" => "2P81751921230411E",
    "status" => "Completed",
    "amount" => "29.95"
  )
);

// Stub
$stubPayPalExpressCheckout = $this->getMockBuilder('Sales\Adapter\PayPal\PayPalExpressCheckout')
             ->disableOriginalConstructor()
             ->getMock();	
			 
$stubPayPalExpressCheckout->expects($this->any())
   ->method('apiTransactionHistory')
   ->will($this->returnValue($apiTransactionHistory));
