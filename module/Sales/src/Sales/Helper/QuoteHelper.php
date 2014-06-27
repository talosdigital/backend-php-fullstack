<?php
namespace Sales\Helper;

use Sales\Document\Quote;
use User\Entity\User\Address;
use User\Entity\User\Phonenumber;
use Geolocation\Document\Geolocation;
use Geolocation\Document\City;
use Sales\Document\Voucher;

use User\Service\UserService;
use Sales\Service\QuoteService;
use Geolocation\Service\GeolocationService;

use Geolocation\Helper\GeolocationHelper;


class QuoteHelper {

	public function __construct($sm) {
		$this->userService = new UserService($sm);
		$this->quoteService = new QuoteService($sm);
		$this->geolocationHelper = new GeolocationHelper($sm);
		$this->geolocationService = new GeolocationService($sm);
	}

	public function getQuote($user){
		$quote = $this->quoteService->findOneBy(array("user.id" => $user->getId()));

		if($quote == null){
			$data['user'] = $user;
			$data['email'] = $user->getEmail();
			if($user->getPhonenumbers()->get(0)){
				$data['phonenumber'] = $user->getPhonenumbers()->get(0);
			} else {
				$data['phonenumber'] = new Phonenumber();
			}
			$quote = new Quote($data);
		}
		return $quote;
	}

	public function addProduct($quote, $data){
		$quote->addItem($data);
		return $quote;
	}

	public function calculateTotals($quote){
 		$items = $quote->getItems();
		$subtotal = 0;
 		$taxes = 0;
		$discount = 0;
 		foreach ($items as $item) {
 			$subtotal += $item->getPrice() * $item->getQuantity();
 			$taxes += $item->getTax() * $item->getQuantity();
 		}
		
		$voucher = $quote->getVoucher();
		if($voucher != null){
			if($voucher['discount_type'] == Voucher::VOUCHER_DISCOUNT_TYPE_PERCENTAGE){
				$discount = $subtotal * $voucher['discount'] / 100;
			} else {
				$discount = $voucher['discount'];
			}	
		} 
 		
		$total = $subtotal - $discount + $taxes;

 		$totals = array(
			'subtotal' => $subtotal,
 			'discount' => $discount,
			'taxes' => $taxes,
 			'total' => $total
 		);

		$quote->setTotals($totals);
		return $quote;
 	}

	public function removeQuote($quote){
 		$this->quoteService->remove($quote);
 	}

 	
 	public function getBillingDetails($user){
 	
 		$address = new Address();
 		
 		if($user->getAddress("Billing")) {
 			// geolocation
 			if($user->getAddress("Billing")->getGeolocation()) {
 				$address->setGeolocation($user->getAddress("Billing")->getGeolocation());
 				// postal code
 				if($user->getAddress("Billing")->getPostCode()){
 					$address->setPostalCode($user->getAddress("Billing")->getPostCode());
 				}
 			}
 			// street name
 			$address->setStreet($user->getAddress("Billing")->getStreet());
 		}
 		if ($user->getFirstname()) {
 			$address->setFirstname($user->getFirstname());
 		}
 		if ($user->getLastname()) {
 			$address->setLastname($user->getLastname());
 		}
 		if($user->getCompanyName()) {
 			$address->setCompanyName($user->getCompanyName());
 		}
 	
 		return $address;
 	}

}