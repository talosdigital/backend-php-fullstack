<?php
namespace SalesTest\Tests;

use SalesTest\AbstractTestCase;

use Sales\Document\Order;
use Sales\Document\Order\TransactionDetails;

use Sales\Service\QuoteService;
use Sales\Service\OrderService;

use Sales\Helper\OrderHelper;
use Sales\Helper\QuoteHelper;

class OrderTest extends AbstractTestCase {

 	protected function alterConfig(array $config) {
 		return $config;
 	}

 	public function setup() {
 		parent::setup();

		$this->quoteService = new QuoteService($this->getServiceManager());
		$this->orderService = new OrderService($this->getServiceManager());
		$this->orderHelper = new OrderHelper($this->getServiceManager());
		$this->quoteHelper = new QuoteHelper($this->getServiceManager());
 	}
	
	// there is no a voucher in this order
 	public function testCreateOrderFromQuote() {
 		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));
 		$order = new Order($quote);

 		// emulating data from a web form

 		// first name
 		$order->setFirstName('Vitaly');
 		$this->assertNotNull($order->getFirstName());
		// last name
 		$order->setLastName('Tkachenko');
 		$this->assertNotNull($order->getLastName());
		// company name
 		$order->setCompany('Talos Digital');
 		$this->assertNotNull($order->getCompanyName());

 		// set items
 		$order->setItems($quote->getItems()->toArray());
 		$this->assertNotNull($order->getItems());

 		// set totals
		$order->setTotals($quote->getTotals());
		$this->assertNotNull($order->getTotals());

		// address
		$order->setBillingDetails($this->quoteHelper->getBillingDetails($quote->getUser()));
		$this->assertNotNull($order->getBillingDetails());

		//status
		$order->setStatus(Order::ORDER_STATUS_PAID);

		//transaction details
		$fakeTransactionDetails = array(
				'message' =>'Thank you for your payment!',
				'status' => TransactionDetails::TRANSACTION_STATUS_SUCCESS,
				'timestamp' => new \DateTime(),
				'reference' => '000003 M'
		);
		$TransactionDetails = new TransactionDetails($fakeTransactionDetails);
		$order->setTransactionDetails($TransactionDetails);
		$this->assertNotNull($order->getTransactionDetails());

		$this->assertNotNull($order);

		// remove a quote
		$this->quoteService->remove($this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000")));
		unset($quote);
		$this->assertNull($this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000")));

 		$this->orderService->save($order);

 	}

 	//check status 
 	 public function testGetStatus() {
 		$order = $this->orderService->findOneBy(array("id" => "51f138f88f604c253e000000"));
 		$this->assertNotNull($order->getStatus());
 		$this->assertEquals('paid', $order->getStatus());
 	} 

 	//check totals
	public function testOrderTotalsTheSameAsInQuote(){
		$quote = $this->quoteService->findOneBy(array("id" => "51eef3be8f604cf11d000000"));
		$order = $this->orderService->findOneBy(array("id" => "51f138f88f604c253e000000"));
		$orderTotals = $order->getTotals();
		$quoteTotals = $quote->getTotals();
		$this->assertEquals($orderTotals, $quoteTotals);
	}

 	//create transaction details and add to order
 	public function testTransactionDetails(){
 		$order = $this->orderService->findOneBy(array("id" => "54f138f88f604c253e000000"));
 		$this->assertEmpty($order->getStatus());

 		$fakeTransactionDetails = array(
 				'message' =>'Thank you for your payment!',
 				'status' => TransactionDetails::TRANSACTION_STATUS_SUCCESS,
 				'timestamp' => new \DateTime(),
 				'reference' => '000003 M'
 		);
 		$TransactionDetails = new TransactionDetails($fakeTransactionDetails);
 		//transaction details
 		$order->setTransactionDetails($TransactionDetails);
 		$this->assertNotNull($order->getTransactionDetails());

 		if($order->getTransactionDetails()){
 			$order->setStatus(Order::ORDER_STATUS_PAID);
 		}

 		// check order status
 		$this->assertNotNull($order->getStatus());
 		$this->assertEquals('paid', $order->getStatus());

 	}

}