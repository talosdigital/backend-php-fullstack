<?php
namespace Sales\Adapter\PayPal;

use Sales\Document\Order\TransactionDetails;

class PayPalExpressCheckout extends PayPalAbstract implements \Sales\Adapter\IPaymentAdapter {
	
	public function __construct($config) {
		$this->apiVersion = $config["api_version"];
		$this->apiUsername = $config['api_username'];
		$this->apiPassword = $config['api_password'];
		$this->apiSignature = $config['api_signature'];
		$this->apiUrl = $config['api_url'];
		if(isset($config['server_url'])) {
			$this->serverUrl = $config['server_url'];
		}
	}
	/**
	 * Make exress checkout request to  PayPal
	 * 
	 * @param Quote $quote
	 * @return string Response (it works as a boolean parameter)
	 */
	public function setExpressCheckoutRequest($quote){
		$totals = $quote->getTotals();
		$billingDetails = $quote->getBillingDetails();
		if(!$billingDetails->getGeolocation()){
			$this->result['L_LONGMESSAGE0'] = "Presented address has not been recognised";
			return self::RESPONSE_ERROR;
		}
		$preparedSetRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'SetExpressCheckout',
				'PAYMENTREQUEST_0_AMT' => '0',
				'RETURNURL' => $this->serverUrl.'/sales/checkout/paypalReturn',
				'CANCELURL' => $this->serverUrl.'/sales/checkout/paypalCancel',
				'L_BILLINGTYPE0' => 'RecurringPayments',
				'L_BILLINGAGREEMENTDESCRIPTION0' => $quote->getItems()->get(0)->getName()." $".(string)$totals['total']." per month.",
				'MAXAMT' => (string)$totals['total'],
				'ADDROVERRIDE' => '1',
				'PAYMENTREQUEST_0_SHIPTOSTREET'=> $billingDetails->getStreet(),
				'PAYMENTREQUEST_0_SHIPTOCITY'=> $billingDetails->getGeolocation()->getCity()->getName(),
				'PAYMENTREQUEST_0_SHIPTOSTATE'=> $billingDetails->getGeolocation()->getCity()->getStateShort(),
				'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'=> $billingDetails->getGeolocation()->getCity()->getCountry()->getCode2(),
				'PAYMENTREQUEST_0_SHIPTOZIP'=> $billingDetails->getPostCode()
		);
		if($billingDetails->getGeolocation()->getCity()->getName() == 'New York'){
			$preparedSetRequest['PAYMENTREQUEST_0_SHIPTOSTATE'] = 'NY';
		}
		if(! $preparedSetRequest["PAYMENTREQUEST_0_SHIPTOZIP"]) {
			$preparedSetRequest["PAYMENTREQUEST_0_SHIPTOZIP"] = "n/a";
		}
		if($this->request($preparedSetRequest) != "Error"){
			return self::RESPONSE_OK;
		} else {
			return self::RESPONSE_ERROR;
		}	
	}
	
	/**
	 * Get as a result payerID parameter from PayPal
	 * 
	 * @param string $token
	 * @return string PAYERID
	 */
	public function getCheckoutDetailsRequest($token){
		$preparedGetDetailsRequest = array(
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'SIGNATURE' => $this->apiSignature,
				'METHOD' => 'GetExpressCheckoutDetails',
				'VERSION' => $this->apiVersion,
				'TOKEN' => $token,
		);
		$this->result = '';
	
		if($this->request($preparedGetDetailsRequest) != "Error"){
			return $this->result['PAYERID'];
		} else {
			return self::RESPONSE_ERROR;
		}
	}
	/**
	 * Create a recurring payment profile
	 * 
	 * @param Quote $quote
	 * @param string $token
	 * @param string $payerId
	 * @return string Response (it works as a boolean parameter)
	 */
	public function createRecurringPaymentsProfileRequest($quote, $token, $payerId){
		$now = new \DateTime();
		$startDate = $now->format(\DateTime::W3C);	
		$totals = $quote->getTotals();
		$preparedCreateProfileRequest = array(
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'SIGNATURE' => $this->apiSignature,
				'METHOD' => 'CreateRecurringPaymentsProfile',
				'VERSION' => $this->apiVersion,
				'TOKEN' => $token,
				'PAYERID' => $payerId,
				'PROFILESTARTDATE' => $startDate,
				'DESC' => $quote->getItems()->get(0)->getName()." $".(string)$totals['total']." per month.",
				'BILLINGPERIOD' => 'Day', // PRODUCTION TEST MODE
				'BILLINGFREQUENCY' => '1',
				'AMT' => (string)$totals['total'],
				'CURRENCYCODE' => 'USD',
				'L_PAYMENTREQUEST_n_ITEMCATEGORY0' => 'Digital',
				'L_PAYMENTREQUEST_n_NAME0' => $quote->getItems()->get(0)->getName(),
				'L_PAYMENTREQUEST_n_AMT0' => (string)$totals['total'],
				'L_PAYMENTREQUEST_n_QTY0' => '1'
		);
		if($this->request($preparedCreateProfileRequest) != "Error"){
			return self::RESPONSE_OK;
		} else {
			return self::RESPONSE_ERROR;
		}
		
	}

	/**
	 * Get transaction history of a recurring payment profile from PayPal
	 * 
	 * @param datetime $startDate
	 * @param string $profileId
	 * @return array result (recurring transactions)
	 */
	public function apiTransactionHistory($startDate, $profileId){
		$startDate = $startDate->format(\DateTime::W3C);
		$preparedTransactionHistoryRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'TransactionSearch',
				'PROFILEID' => $profileId,
				'TRANSACTIONCLASS' => 'Received',
				'STARTDATE' => $startDate,
		);
	
		if($this->request($preparedTransactionHistoryRequest) != "Error"){
			// map results (api limit 100)
			$results = array();
			for($i=0; $i<100; $i++) {
				if(isset($this->result["L_TIMESTAMP".$i])) {
					$results[$i]["timestamp"] = $this->result["L_TIMESTAMP".$i];
				}
				if(isset($this->result["L_TRANSACTIONID".$i])) {
					$results[$i]["transaction_id"] = $this->result["L_TRANSACTIONID".$i];
				}
				if(isset($this->result["L_STATUS".$i])) {
					$results[$i]["status"] = $this->result["L_STATUS".$i];
				}
				if(isset($this->result["L_AMT".$i])) {
					$results[$i]["amount"] = $this->result["L_AMT".$i];
				}
			}
			return $results;
		} else {
			return $this->getErrorMessage();
		}
	}
	
	/**
	 * Get data of a particular transaction from PayPal
	 * 
	 * @param string $transactionId
	 * @return array transaction details
	 */
	public function apiTransactionDetails($transactionId){
		$preparedTransactionDetailsRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'GetTransactionDetails',
				'TRANSACTIONID' => $transactionId
		);
	
		if($this->request($preparedTransactionDetailsRequest) != "Error"){
			return $results;
		} else {
			return $this->getErrorMessage();
		}
	}
	
	
	
}