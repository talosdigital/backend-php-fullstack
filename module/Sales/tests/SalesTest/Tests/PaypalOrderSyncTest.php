<?php
namespace SalesTest\Tests;

use SalesTest\AbstractTestCase;

use Subscription\Document\Subscription;

use Sales\Document\Quote;
use Sales\Document\Item;
use Sales\Document\Voucher;

use Sales\Adapter\PayPal\PayPalExpressCheckout;

use Sales\Helper\QuoteHelper;
use Sales\Helper\OrderHelper;
use Subscription\Helper\SubscriptionHelper;
use Sales\Helper\PaymentHelper;

use User\Service\UserService;
use Sales\Service\QuoteService;
use Sales\Service\OrderService;

class PaypalOrderSyncTest extends AbstractTestCase {

 	protected function alterConfig(array $config) {
 		return $config;
 	}

 	public function setup() {
 		parent::setup();

		$this->userService = new UserService($this->getServiceManager());
		$this->quoteService = new QuoteService($this->getServiceManager());
		$this->orderService = new OrderService($this->getServiceManager());
		$this->quoteHelper = new QuoteHelper($this->getServiceManager());
		$this->orderHelper = new OrderHelper($this->getServiceManager());
		$this->subscriptionHelper = new SubscriptionHelper($this->getServiceManager());
		$this->paymentHelper = new PaymentHelper($this->getServiceManager());
  	}

	public function testUserHasOrder() {
		$userId = "51f6d2b18f604cee0a000000";
		$order = $this->orderService->findOneBy(array("user.id" => $userId));
		$this->assertNotNull($order);
	}

	public function testUserHasSubscription() {
		$userId = "51f6d2b18f604cee0a000000";
		$user = $this->userService->findOneBy(array("id" => $userId));

		$subscriptionId = null;
		foreach($user->getSubscriptions() as $subscription) {
			if($subscription->getRecurringProfileId()) {
				$subscriptionId = $subscription->getRecurringProfileId();		
			}
		}
		$this->assertNotNull($subscriptionId);
	}
	
	public function testPaypalReturnTransactions() {
		$userId = "51f6d2b18f604cee0a000000";
		$user = $this->userService->findOneBy(array("id" => $userId));
		$recurringProfileId = "I-SRDP8SP46N4E";
		
		// Paypal gateway
		include("stubs/PayPalExpressCheckout.php");
		$stubGateway = $stubPayPalExpressCheckout;

		$transactions = $this->orderHelper->retrieveLastTransactions($stubGateway, $user, $recurringProfileId);	
        $this->assertEquals(6,count($transactions));			
		$transaction = $transactions[5];			
        $this->assertEquals("2013-08-13T12:08:17Z", $transaction["timestamp"]);
        $this->assertEquals("2P81751921230411E", $transaction["transaction_id"]);
        $this->assertEquals("Completed", $transaction["status"]);
        $this->assertEquals("29.95", $transaction["amount"]);
	}
	
	public function testSyncOrders() {
		$userId = "51f6d2b18f604cee0a000000";
		$user = $this->userService->findOneBy(array("id" => $userId));
		$recurringProfileId = "I-SRDP8SP46N4E";
		
		$orders = $this->orderService->findBy(array("user.id" => $userId, "transaction_details.profile_id" => $recurringProfileId));
		$count = $orders->count();

		// Paypal gateway
		include("stubs/PayPalExpressCheckout.php");
		$stubGateway = $stubPayPalExpressCheckout;

		// Paypal get last transactions
		$transactions = $this->orderHelper->retrieveLastTransactions($stubGateway, $user, $recurringProfileId);	
		
		// Create new trasactions as billed orders
		$result = $this->orderHelper->syncTransactions($user, $recurringProfileId, $transactions);
		$this->assertEquals($count + 4, count($result));
		$orders = $this->orderService->findBy(array("user.id" => $userId, "transaction_details.profile_id" => $recurringProfileId));
		$this->assertCount($count + 4, $orders);
		
		$order1 = $this->orderService->findOneBy(array("id" => $result[0]['order_id']));
		$order2 = $this->orderService->findOneBy(array("id" => $result[3]['order_id']));
		$this->assertEquals($order1->getBillingDetails(), $order2->getBillingDetails());
		$this->assertEquals($order1->getTotals(), $order2->getTotals());
		$this->assertEquals($order1->getItems()->toArray(), $order2->getItems()->toArray());
	
	}

}