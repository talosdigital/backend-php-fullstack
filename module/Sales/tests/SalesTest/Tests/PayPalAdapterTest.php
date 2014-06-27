<?php
namespace SalesTest\Tests;

use SalesTest\AbstractTestCase;

use Sales\Service\QuoteService;

use Sales\Adapter\PayPal\PayPalPaymentsPro;
use Sales\Adapter\PayPal\PayPalExpressCheckout;

class PayPalAdapterTest extends AbstractTestCase {

	protected function alterConfig(array $config) {
		return $config;
	}

	public function setup() {
		parent::setup();
		$this->quoteService = new QuoteService($this->getServiceManager());
	}
	
	/*PayPalExpressCheckout tests*/
	/**
	 * setExpressCheckout request 
	 * 
	 */
	public function testSetExpressCheckoutRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "94.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$serverURL = explode('/',$_SERVER['PWD']);
		$config["server_url"] = 'http://'.$serverURL[4];
		$gateway = new PayPalExpressCheckout($config);
		$result = $gateway->setExpressCheckoutRequest($quote);
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
	
	}
	
	/**
	 * getCheckoutDetails request 
	 * 
	 */
	public function testGetCheckoutDetailsRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "94.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$serverURL = explode('/',$_SERVER['PWD']);
		$config["server_url"] = 'http://'.$serverURL[4];
		$gateway = new PayPalExpressCheckout($config);
		$this->assertNotEquals('Error', $gateway->setExpressCheckoutRequest($quote));
		$token = $gateway->result['TOKEN'];
		$result = $gateway->getCheckoutDetailsRequest($token);
		$this->assertNotEquals('Error', $result);
	}
	
	/**
	 * Create a recurring payments profile
	 * 
	 */
	public function testCreateRecurringPaymentsProfileRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "94.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$serverURL = explode('/',$_SERVER['PWD']);
		$config["server_url"] = 'http://'.$serverURL[4];
		$gateway = new PayPalExpressCheckout($config);
		$this->assertNotEquals('Error', $gateway->setExpressCheckoutRequest($quote));
		$token = $gateway->result['TOKEN'];
		$result = $gateway->createRecurringPaymentsProfileRequest($quote, $token, "PAYERID");
		$this->assertNotNull($result);
	
	}
	
	/*PayPalPaymentsPro tests*/
	/**
	 * Send request to PayPal
	 * 
	 */
	public function testSendRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "94.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$fakeCreditCardDetails = array(
				'credit_card_number' => '4470468677359491',
				'creditcard_type' => 'Visa',
				'cvv2' => '123',
				'cardholder_name' => 'Unit Test',
				'expiry_date_day' => '07',
				'expiry_date_year' => '2018'
		);
	
		$gateway = new PayPalPaymentsPro($config);
	
		$result = $gateway->sendRequest($quote, "recurring", $fakeCreditCardDetails);
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
	
	}
	
	/**
	 * Get a recurring payments profile details
	 * 
	 */
	public function testGetRecurringPaymentsProfileDetailsRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "94.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$fakeCreditCardDetails = array(
				'credit_card_number' => '4470468677359491',
				'creditcard_type' => 'Visa',
				'cvv2' => '123',
				'cardholder_name' => 'Unit Test',
				'expiry_date_day' => '07',
				'expiry_date_year' => '2018'
		);
	
		$gateway = new PayPalPaymentsPro($config);
	
		$result = $gateway->sendRequest($quote, "recurring", $fakeCreditCardDetails);
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
		$profileId = $gateway->result['PROFILEID'];
		$result = $gateway->GetRecurringPaymentsProfileDetailsRequest($profileId);
		$this->assertNotNull($result);
		$this->assertNotNull($result['STATUS']);
		$this->assertEquals('Active', $result['STATUS']);
	}
	
	/**
	 * Update a recurring payments profile
	 * 
	 */
	public function testUpdateRecurringPaymentsProfileRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "56.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$fakeCreditCardDetails = array(
				'credit_card_number' => '4470468677359491',
				'creditcard_type' => 'Visa',
				'cvv2' => '123',
				'cardholder_name' => 'Unit Test',
				'expiry_date_day' => '07',
				'expiry_date_year' => '2018'
		);
	
		$gateway = new PayPalPaymentsPro($config);
	
		$result = $gateway->sendRequest($quote, "recurring", $fakeCreditCardDetails);
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
		$profileId = $gateway->result['PROFILEID'];
		$data = array(
				'street' =>'Richmond Hill, ON, Canada',
				'city' => 'Toronto',
				'state' => 'ON',
				'countrycode' => 'CA',
				'post_code' => '12345'
		);
		$result = $gateway->updateRecurringPaymentsProfileRequest($profileId, $data);
		$this->assertNotNull($result);
	}
	
	/**
	 * Manage a recurring payments profile status
	 * 
	 */
	public function testManageRecurringPaymentsProfileStatusRequest(){
		$quote = $this->quoteService->findOneBy(array("id" => "520d21c28f604c450e000000"));
	
		$config = array(
				"api_version" => "56.0",
				"api_username" => "merchant_api1.talosdigital.com",
				"api_password" => "1373651564",
				"api_signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31AzTYRCB9udeXL.KXqVaFdbyN8mwn",
				"api_url" => "https://api-3t.sandbox.paypal.com/nvp",
				"url" => "https://www.sandbox.paypal.com/cgi-bin/webscr",
		);
		$fakeCreditCardDetails = array(
				'credit_card_number' => '4470468677359491',
				'creditcard_type' => 'Visa',
				'cvv2' => '123',
				'cardholder_name' => 'Unit Test',
				'expiry_date_day' => '07',
				'expiry_date_year' => '2018'
		);
	
		$gateway = new PayPalPaymentsPro($config);
	
		$result = $gateway->sendRequest($quote, "recurring", $fakeCreditCardDetails);
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
		$profileId = $gateway->result['PROFILEID'];
		$statusBefore = $gateway->result['PROFILESTATUS'];
		$result = $gateway->manageRecurringPaymentsProfileStatusRequest($profileId, "Cancel");
		$this->assertNotNull($result);
		$this->assertNotEquals('Error', $result);
		$result2 = $gateway->getRecurringPaymentsProfileDetailsRequest($profileId);
		$statusAfter = $result2['STATUS'];
		$this->assertNotEquals($statusBefore, $statusAfter);
	}
	
}