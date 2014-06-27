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

class PaypalSubscriptionStatus extends AbstractTestCase {

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

	public function testAllUserSubscriptions(){
		$users = $users = $this->userService->findBy( array( "subscriptions.recurring_profile_id" => array('$ne' => null)));
		foreach($users as $user){
			$subscription = $this->subscriptionHelper->getActive($user);
			$status = $this->paymentHelper->checkPaymentStatus($subscription->getRecurringProfileId());
			if($status != Subscription::SUBSCRIPTION_STATUS_ACTIVE){
				$this->subscriptionHelper->cancelAll($user);
			}
		} 
	}
		
}