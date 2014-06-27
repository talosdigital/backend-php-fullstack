<?php
namespace SalesTest\Tests;

use SalesTest\AbstractTestCase;

use Sales\Document\Quote;
use Sales\Document\Item;
use Sales\Document\Voucher;

use Sales\Helper\QuoteHelper;

use User\Service\UserService;
use Sales\Service\QuoteService;

class QuoteTest extends AbstractTestCase {

 	protected function alterConfig(array $config) {
 		return $config;
 	}

 	public function setup() {
 		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
		$this->quoteService = new QuoteService($this->getServiceManager());
		$this->quoteHelper = new QuoteHelper($this->getServiceManager());
  	}

 	/**
 	 * Create a quote
 	 */
 	public function testCreateQuote(){
		$user = $this->userService->findOneBy(array("id" => "51effc658f604c012b000007"));
 		$data['user'] = $user;
		$quote = $this->quoteHelper->getQuote($user);

 		$this->assertNotNull($quote);
 	}
	
	/**
	 * Add a product to the quote
	 */
 	public function testAddProductToQuote(){
 		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));

 		// create item
 		$itemData = array(
 			'sku' => '2343564572',
 			'name' => '30 days subscription',
 			'price' => 30.00,
 			'tax' => 2.5,
 			'quantity' => 1,
 			'property' => 30
 		);
 		$item = new Item($itemData);
 		$this->assertNotNull($item);
		$quote = $this->quoteHelper->addProduct($quote, $item);
 		$this->quoteService->save($quote);
 		$this->assertContains($item, $quote->getItems());

		$data['totals'] = array(
				'total' => 30,
				'subtotal' => 27,
				'taxes' => 3
		);

		$quote->setTotals($data["totals"]);
		$this->quoteService->save($quote);
	 }

	/**
	 * Add several products to the quote
	 */
 	public function testAddSeveralProductsToQuote() {
 		$quote = $this->quoteService->findOneBy(array("id" => "51eefd2a8f604cd91b000000"));

 		$items = array(
			1 => array(
				'sku' => '2343564572',
				'name' => '30 days subscription',
				'price' => 30.00,
				'tax' => 2.5,
				'quantity' => 1,
				'property' => 30
			),
			2 => array(
				'sku' => '2adfdsfsd64572',
				'name' => '60 days subscription',
				'price' => 60.00,
				'tax' => 5.00,
				'quantity' => 2,
				'property' => 60
			),
		);
 		foreach ($items as $item){
			$quote = $this->quoteHelper->addProduct($quote, new Item($item));
 		}
 		$this->quoteService->save($quote);

 		$this->assertNotNull($quote->getItems());
		$this->assertEquals(2, $quote->getItems()->count());
	}

	/**
	 * Cheking totals
	 */
	 public function testTotalAmount() {
	 	$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));

		$this->quoteHelper->calculateTotals($quote);
		
		$totals = $quote->getTotals();
		
		$this->assertEquals(150, $totals['subtotal']);
		$this->assertEquals(12.5, $totals['taxes']);
		$this->assertEquals(162.5, $totals['total']);
	}

	/**
	 * Return an array of totals
	 * @return array
	 */
 	public function testCalculateTotals(){
 		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));

		$this->quoteHelper->calculateTotals($quote);
 		
 		$totals = $quote->getTotals();
		
		$total = $totals['subtotal'] + $totals['taxes'];
 		$this->assertEquals(150, $totals['subtotal']);
 		$this->assertEquals(12.5, $totals['taxes']);
 		$this->assertEquals(162.5, $total);
	}

	/**
	 * Remove a quote (after a quote became an order)
	 */
  	public function testRemoveQuote(){
 		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));
 		$this->quoteHelper->removeQuote($quote);

 		$this->assertNull($this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000")));
 	}


 	/**
 	 *  Cheching billing address
 	 */
 	public function testBillingDetails(){
 		$user = $this->userService->findOneBy(array("id" => "51effc658f604c012b000007"));
 		$this->assertNotNull($user);
		// company name
 		$companyName = $user->getCompanyName();
 		$this->assertNotNull($companyName);
 		$this->assertEquals('Test Inc.', $companyName);
		// address
 		$address = $user->getAddresses()->get(0)->getStreet();
 		$this->assertNotNull($address);
 		$this->assertEquals('Pionerskaya st. 52', $address);
		// postal code
 		$postal_code = $user->getAddresses()->get(0)->getPostalCode();
		if(empty($postal_code))
			$postal_code = $user->getAddresses()->get(0)->getGeolocation()->getPostCode();
		$this->assertNotNull($postal_code);
		// city
 		$city = $user->getAddresses()->get(0)->getGeolocation()->getCity()->getName();
 		$this->assertNotNull($city);
 		$this->assertEquals('Stavropol', $city);
		// state
 		$state = $user->getAddresses()->get(0)->getGeolocation()->getCity()->getState();
 		$this->assertNotNull($state);
 		$this->assertEquals('Stavropol Krai', $state);
		// country
 		$country = $user->getAddresses()->get(0)->getGeolocation()->getCity()->getCountry()->getName();
 		$this->assertNotNull($country);
 		$this->assertEquals('Russian Federation', $country);

 	}

	/**
	 * get User check
	 */
 	 public function testGetUser() {
 		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));
 		$user = $quote->getUser();
 		$this->assertNotNull($user->getLastname());
 	}








}