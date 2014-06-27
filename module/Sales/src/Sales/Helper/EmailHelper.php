<?php
namespace Sales\Helper;

class EmailHelper {

	public function newOrder($event) {
		try {
			// Service/Helper
			$this->email = $event->getTarget()->getServiceLocator()->get("email");
	
			// Params
			$params = $event->getParams();
			$order = $params["order"];

			/*
			$email = $this->email->create(array(
									"fullName" => $order->getUser()->getFullName(),
									"message" => $order->getTransactionDetails()->getReference()
								));
								
			$email->setSubject("Subscription Payment Success");
			$email->addTo($order->getUser()->getEmail());
			$this->email->send($email);
			 * 
			 */
		}
		catch(\Exception $e) {
			
		}
	}
				
}