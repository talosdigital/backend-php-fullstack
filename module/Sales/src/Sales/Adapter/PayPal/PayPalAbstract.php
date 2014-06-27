<?php
namespace Sales\Adapter\Paypal;

use Sales\Adapter\AbstractPaymentAdapter;
use Sales\Document\Order\TransactionDetails;

class PayPalAbstract extends AbstractPaymentAdapter {
	
	public $result ='';

	/**
	 * Manage a recurring payment profile (actions such as "Cancel", "Suspend" and "Reactivate" are supported)
	 * 
	 * @param string $profileId
	 * @param string $newStatus
	 * @return string Response (it works as a boolean parameter)
	 */
	public function manageRecurringPaymentsProfileStatusRequest($profileId, $newStatus){
		$preparedManageProfileStatusRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'ManageRecurringPaymentsProfileStatus',
				'PROFILEID' => $profileId,
				'ACTION' => $newStatus
		);
	
		if ($this->request($preparedManageProfileStatusRequest) != "Error"){
			return self::RESPONSE_OK;
		} else {
			return self::RESPONSE_ERROR;
		}
	}
	
	/**
	 * Update billing address of a recurring payment profile 
	 * 
	 * @param string $profileId
	 * @param string $data
	 * @return string Response (it works as a boolean parameter)
	 */
	public function updateRecurringPaymentsProfileRequest($profileId, $data){
		
		$preparedUpdateProfileRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'UpdateRecurringPaymentsProfile',
				'PROFILEID' => $profileId,
		);
	
		if($data['street']){
			$preparedUpdateProfileRequest['STREET'] = $data['street'];
			$preparedUpdateProfileRequest['SHIPTOSTREET'] = $data['street'];
		}
		if($data['city']){
			$preparedUpdateProfileRequest['CITY'] = $data['city'];
			$preparedUpdateProfileRequest['SHIPTOCITY'] = $data['city'];
		}
		if($data['state']){
			$preparedUpdateProfileRequest['STATE'] = $data['state'];
			$preparedUpdateProfileRequest['SHIPTOSTATE'] =  $data['state'];
		}
		if($data['countrycode']){
			$preparedUpdateProfileRequest['COUNTRYCODE'] = $data['countrycode'];
			$preparedUpdateProfileRequest['SHIPTOCOUNTRY'] = $data['countrycode'];
		}
		if($data['post_code']){
			$preparedUpdateProfileRequest['ZIP'] = $data['post_code'];
			$preparedUpdateProfileRequest['SHIPTOZIP'] = $data['post_code'];
		}
		
		if($data['creditcard_type']){
			$preparedUpdateProfileRequest['CREDITCARDTYPE'] = $data['creditcard_type'];
		}
		
		if($data['credit_card_number']) {
			$preparedUpdateProfileRequest['ACCT'] = $data['credit_card_number'];
		}
		
		if($data['expdate']) {
			$preparedUpdateProfileRequest['EXPDATE'] = $data['expdate'];
		}
		
		if($data['cvv2']) {
			$preparedUpdateProfileRequest['CVV2'] = $data['cvv2'];
		}
	
		if($this->request($preparedUpdateProfileRequest) != "Error"){	
			return self::RESPONSE_OK;
		} else {
			return self::RESPONSE_ERROR;
		}
	}

	/**
	 * Process sending a request to a payment gateway
	 * 
	 * @return string
	 */
	protected function request($preparedRequest){
		
		$request = '';
		$request = http_build_query($preparedRequest);
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
		$response = curl_exec($curl);
		curl_close($curl);

		$this->result = $this->responseToArray($response);		
		
		if($this->isTransactionApproved()){
			return self::RESPONSE_OK;
		}
		else{
			return $this->getErrorMessage();
		}
	}

	/**
	 * Return response as an array
	 * 
	 * @param String $response
	 * @return Array
	 */
	public function responseToArray($nvp_string ) {
   		$proArray = array();
   		while(strlen($nvp_string)) {
      		$keypos= strpos($nvp_string,'=');
      		$keyval = substr($nvp_string,0,$keypos);
      		$valuepos = strpos($nvp_string,'&') ? strpos($nvp_string,'&'): strlen($nvp_string);
      		$valval = substr($nvp_string,$keypos+1,$valuepos-$keypos-1);
      		$proArray[$keyval] = urldecode($valval);
      		$nvp_string = substr($nvp_string,$valuepos+1,strlen($nvp_string));
   		}
   		return $proArray;
	}

	/**
	 * Get an error message from PayPal
	 * 
	 */
	public function getErrorMessage(){
		if (!empty($this->result)) {
			return $this->result['L_LONGMESSAGE0'];
		}	
	}
	
	/**
	 * Get Transaction details
	 * 
	 * @return \Sales\Document\Order\TransactionDetails
	 */
	public function getTransactionDetails(){
		$data = array(
				'message' =>$this->result['ACK'],
				'status' => TransactionDetails::TRANSACTION_STATUS_SUCCESS,
				'timestamp' => $this->result['TIMESTAMP']
		);
		if(!empty($this->result['TRANSACTIONID'])){
			$data['profile_id'] = $this->result['TRANSACTIONID'];
		}
		return new TransactionDetails($data);
	}

	/**
	 * Check whether the transaction was success or not
	 * 
	 * @return boolean
	 */
	public function isTransactionApproved(){
		if(empty($this->result['L_LONGMESSAGE0'])) {
				return true; 
		}
		else {
			return false;
		}
	}

	/**
	 * Retrieve a recurring payment profile from PayPal
	 * 
	 * @param string $profileId
	 * @return array result (a recurring profile data)
	 */
	public function getRecurringPaymentsProfileDetailsRequest($profileId){
		$preparedGetProfileDetailsRequest = array(
				'VERSION' => $this->apiVersion,
				'SIGNATURE' => $this->apiSignature,
				'USER' => $this->apiUsername ,
				'PWD' => $this->apiPassword,
				'METHOD' => 'GetRecurringPaymentsProfileDetails',
				'PROFILEID' => $profileId,
		);
		if($this->request($preparedGetProfileDetailsRequest) != "Error"){
			return $this->result;
		} else {
			return  $this->getErrorMessage();
		}
	}
	
}
