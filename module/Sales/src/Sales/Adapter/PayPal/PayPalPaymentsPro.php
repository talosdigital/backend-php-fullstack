<?php

namespace Sales\Adapter\PayPal;

class PayPalPaymentsPro extends PayPalAbstract implements \Sales\Adapter\IPaymentAdapter {
	
	private $api_version;
	private $api_username;
	private $api_password;
	private $api_signature;
	private $url;
	
	
	public function __construct($config) {
		$this->apiVersion = $this->api_version = $config["api_version"];
		$this->apiUsername = $this->api_username = $config['api_username'];
		$this->apiPassword = $this->api_password = $config['api_password'];
		$this->apiSignature = $this->api_signature = $config['api_signature'];
		$this->apiUrl = $this->url = $config['api_url'];
	}
	
	/**
	 * Prepare a request to a particular state
	 * 
	 * @param Quote $quote
	 * @param String $transactionType
	 * @param Array $creditCardDetails
	 */
	
	public function sendRequest($quote, $transactionType, $creditCardDetails){
		
		$totals = $quote->getTotals();
		$startDate = new \DateTime('NOW');
		
		if($transactionType == self::TRANSACTION_TYPE_PURCHASE){
			$preparedRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'DoDirectPayment',
				'PAYMENTACTION' => 'Sale',
				'IPADDRESS' => $_SERVER['REMOTE_ADDR'],
				'AMT' => (string)$totals['total'], //cannot be more than 10,000
				'CREDITCARDTYPE' => $creditCardDetails['creditcard_type'],
				'ACCT' => $creditCardDetails["credit_card_number"],
				'EXPDATE' =>  $creditCardDetails["expiry_date_day"] . $creditCardDetails["expiry_date_year"],
				'CVV2' => $creditCardDetails["cvv2"], // has to be exactly 3 digits or 4 digits for American Express
				'FIRSTNAME' => $quote->getUser()->getName(),
				'LASTNAME' => '',
				'STREET' => $quote->getBillingDetails()->getStreet(),
				'ZIP' => $quote->getBillingDetails()->getPostalCode(),
				'CURRENCYCODE' => 'USD',
				'DESC' => $quote->getItems()->get(0)->getName()
			);
			if($quote->getBillingDetails()->getGeolocation()) {
				$preparedRequest['CITY'] = $quote->getBillingDetails()->getGeolocation()->getCity()->getName();
				$preparedRequest['STATE'] = $quote->getBillingDetails()->getGeolocation()->getCity()->getState();
				$preparedRequest['COUNTRYCODE'] = $quote->getBillingDetails()->getGeolocation()->getCity()->getCountry()->getCode2();

			}
		}
		
		if($transactionType == self::TRANSACTION_TYPE_RECURRING){
			
			  $preparedRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'CreateRecurringPaymentsProfile',
				'CREDITCARDTYPE' => $creditCardDetails['creditcard_type'],
				'ACCT' => $creditCardDetails["credit_card_number"],
				'EXPDATE' =>  $creditCardDetails["expiry_date_day"] . $creditCardDetails["expiry_date_year"],
				'CVV2' => $creditCardDetails["cvv2"], 
				'FIRSTNAME' => $quote->getUser()->getName(),
				'LASTNAME' => '',
				'CURRENCYCODE' => 'USD',
				'PROFILESTARTDATE' => $startDate->format('c'),
				'DESC' => $quote->getItems()->get(0)->getName(), 								
				'BILLINGPERIOD' => 'Month',
				'BILLINGFREQUENCY' => '1',
				'AMT' => (string)$totals['total'], 
				'EMAIL' => $quote->getUser()->getEmail(),
				//'L_PAYMENTREQUEST_0_ITEMCATEGORY0' => 'Digital', // There was following error "You are not signed up to accept payment for digitally delivered goods"
				'L_PAYMENTREQUEST_0_NAME0' => $quote->getItems()->get(0)->getName(),
				'L_PAYMENTREQUEST_0_AMT0' => (string)$totals['total'],
				'L_PAYMENTREQUEST_0_QTY0' => '1'
			);  
		}

		if($transactionType == self::TRANSACTION_TYPE_AUTHORIZATION){
			$request = array(
			// some code here		
			);
		}
		
		$response = $this->request($preparedRequest);
		if($response == self::RESPONSE_OK){
			return self::RESPONSE_OK;
		} 
		else {
			return $response;
		}	
	}
	
	public function createRecurringPaymentsProfileRequest($quote, $token, $payerId) {
		// this should be in use
	}

}