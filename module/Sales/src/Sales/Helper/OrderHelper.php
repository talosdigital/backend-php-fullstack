<?php
namespace Sales\Helper;

use Zend\View\Model\ViewModel;

use Sales\Document\Order;
use Sales\Document\Order\TransactionDetails;
use User\Document\User\Address;

use Sales\Service\QuoteService;
use Sales\Service\OrderService;

class OrderHelper {

	public function __construct($sm) {
		$this->serviceLocator = $sm;
		//$this->email = $sm->get("email");
		$this->quoteService = new QuoteService($sm);
		$this->orderService = new OrderService($sm);
	}
	
	public function getServiceLocator() {
		return $this->serviceLocator;
	}

	public function createOrderFromQuote($quote) {
		$order = new Order();
		$order->setUser($quote->getUser());
		$order->setEmail($quote->getEmail());
		$items = $quote->getItems();
		$order->getItems()->clear();
		foreach($items as $item){
			$order->getItems()->add($item);
		}
		$order->setPhonenumber($quote->getPhonenumber());
		$order->setVoucher($quote->getVoucher());
		$order->setTotals($quote->getTotals());
		$order->setBillingDetails($quote->getBillingDetails());
		$order->setStatus(Order::ORDER_STATUS_PAID);
		
		$this->quoteService->remove($quote);

		return $order;
	}
	
	public function getOrderHistory($user){
		$orders = $this->orderService->findBy(array("user.id" => $user->getId()));
		$results = array();
		$yearRow = array();
		$year = new \DateTime('now');
		$year = $year->format("Y");
		foreach($orders as $order){
			$totals = $order->getTotals();
			if($year != $order->getTransactionDetails()->getTimestamp()->format("Y")){
				$yearRow = array();
				$year = $order->getTransactionDetails()->getTimestamp()->format("Y");
			}
			$result = array(
				'date' => $order->getTransactionDetails()->getTimestamp()->format("d F Y"),
				'total' => $totals['total'],
				'items' => $order->getItems()->get(0)->getName(),
				'id' => $order->getId() 	
			);
			array_push($yearRow, $result);
			arsort($yearRow);
			$results[$year] = array('year' => $year, 'data' => $yearRow);
		}

		krsort($results);
		
		return $results;
	}

	public function getLastOrder($user){
		$orders = $this->orderService->findBy(array(
							"user.id" => $user->getId() 
						));
		$lastDate = null;
		$lastOrder = null;
		
		foreach($orders as $order) {
			if($order->getCreatedAt() > $lastDate) {
				$lastOrder = $order; 
			}
		}
		
		return $lastOrder;
	}
	
	public function retrieveLastTransactions($gateway, $user, $recurringProfileId) {
		$transactions = array();
		
		$orders = $this->orderService->findBy(array(
											"user.id" => $user->getId(), 
											"transaction_details.profile_id" => $recurringProfileId), 
										array("created_at" => "DESC")
									);
		// it's required an order as reference
		if($orders->count()) {
			$lastOrder = $orders->getNext();
			$startDate = new \DateTime();
			if($lastOrder->getCreatedAt()) {
				$startDate = $lastOrder->getCreatedAt();
			}
			$startDate = $startDate->add(\DateInterval::createFromDateString('yesterday'));
			$transactions = $gateway->apiTransactionHistory($startDate, $recurringProfileId);
		}
		
		return $transactions;
	}
	
	public function syncTransactions($user, $recurringProfileId, $transactions) {
		$results = array();
		
		// First order to set the Transaction Id
		$firstOrder = $this->orderService->findOneBy(array(
							"user.id" => $user->getId(), 
							"transaction_details.reference" => null, 
							"transaction_details.profile_id" => $recurringProfileId
						));
		
		// Last order to use as template
		$templateOrder = $this->orderService->findOneBy(array(
							"user.id" => $user->getId(), 
							"transaction_details.profile_id" => $recurringProfileId
						));
		if(is_array($transactions)) {
			foreach($transactions as $transaction) {
				if($transaction["status"] == "Completed") {
					
					// Save TransactionId first order 
					if($firstOrder) {
						$firstOrder->getTransactionDetails()->setReference($transaction["transaction_id"]);
						$this->orderService->save($firstOrder);
	
						// Report
						$result["order_id"] = $firstOrder->getId();
						$result["action"] = "Update order";
						$result["transaction_id"] = $transaction["transaction_id"];
						$results[] = $result;
	
						$firstOrder = null;
					}
					else {
						// Save additional orders
						$order = $this->orderService->findOneBy(array("user.id" => $user->getId(), 
																				"transaction_details.reference" => $transaction["transaction_id"],
																				"transaction_details.profile_id" => $recurringProfileId
																				));
						if(! $order) {
							// Create order
							$order = $templateOrder->cloneMe();
							
							// Set transaction details
							$transactionDetails = new TransactionDetails();
							$transactionDetails->setReference($transaction["transaction_id"]);
							$transactionDetails->setTimestamp($transaction["timestamp"]);
							$transactionDetails->setStatus($transaction["status"]);
							$transactionDetails->setProfileId($recurringProfileId);
							$order->setTransactionDetails($transactionDetails);
							
							// Save
							$order = $this->orderService->save($order);
							
							// Email
							$recipients = $user->getSettings("sales/order_recipient_emails");
							$this->emailInvoice($recipients, $order);
							
							// Report
							$result["order_id"] = $order->getId();
							$result["action"] = "New order";
							$result["transaction_id"] = $transaction["transaction_id"];
							$results[] = $result;
						}
					}
				}
			}
		}
		return $results;
	}

	/**
	 * This method email with the invoice details to the specific recipients.
	 * 
	 * @param $recipients : Array (name, email)
	 * @param $order : Sales\Document\Order
	 * 
	 */
	public function emailInvoice($recipients, $order) {
		$config = $this->getServiceLocator()->get("Config");
		$vars = $config["email"]["template_vars"];
		$vars["order"] = $order;
		try {
    		$viewRender = $this->getServiceLocator()->get('ViewRenderer');
    		$emailViewModel = new ViewModel($vars);
    		$emailViewModel->setTemplate("sales/html/receipt");
    		$emailLayout = new ViewModel($vars);
    		$emailLayout->setTemplate("email/layout/html/layout");
           	$emailLayout->setVariable("content", $viewRender->render($emailViewModel));
        	$html = $viewRender->render($emailLayout);
    
    		// Send to order user
    		$email = $this->email->create(array("html_content" => $html));
    		$email->setSubject($vars["company"]." Invoce ".$order->getInvoiceId());
    		$email->addTo($order->getUser()->getEmail());
    		$this->email->send($email);
    
    		// Send to additional recipients
    		if(is_array($recipients)) {
    			foreach($recipients as $recipient) {
    				$email = $this->email->create(array("html_content" => $html));
    				$email->setSubject($vars["company"]." Invoce ".$order->getInvoiceId());
    				$email->addTo($recipient);	
    				$this->email->send($email);
    			}
    		}
		} catch (\Exception $e) {
			
		}
	}

}
