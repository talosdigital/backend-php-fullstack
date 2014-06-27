<?php
namespace Sales\Adapter\FirstData;

use Sales\Document\Order\TransactionDetails;

class FirstDataPayment extends \Sales\Adapter\AbstractPaymentAdapter implements \Sales\Adapter\IPaymentAdapter {

	private $soap_api;
	private $exactId;
	private $gatewayPwd;

	public function __construct($config) {
		$this->soap_api = new SoapApi($config);
		$this->exactId = $config["exact_id"];
		$this->gatewayPwd = $config["gateway_password"];
	}

	public function prepareRequest($quote, $transactionType = "purchase", $creditCardDetails){
		if($transactionType == self::TRANSACTION_TYPE_PURCHASE) {
			$transaction_type = 00;
		}
		else if($transactionType == self::TRANSACTION_TYPE_AUTHORIZATION) {
			$transaction_type = 01;
		}

		if ($quote->getBillingDetails()) {
			$postal_code = $quote->getBillingDetails()->getPostalCode();
		}
		else {
			$postal_code = '';
		}
		$totals = $quote->getTotals();

		$this->request = array(
				"User_Name"=>$quote->getUser()->getFullname(),
				"ExactID"=>$this->exactId,				    	//Payment Gateway
				"Password"=>$this->gatewayPwd,					//Gateway Password
				"Transaction_Type"=>'00',
				"Reference_No"=>0,
				"Customer_Ref"=>0,
				"Reference_3"=>0,
				"Client_IP"=>$_SERVER['REMOTE_ADDR'],
				"Client_Email"=>$quote->getUser()->getEmail(),
				"Language"=>"en",
				"Card_Number"=>$creditCardDetails["number"],
				"Expiry_Date"=>$creditCardDetails["expiry_date_day"] . $creditCardDetails["expiry_date_year"], //This value should be in the format MM/YY. (Currently its in a MM/YYYY)
				"CardHoldersName"=>$creditCardDetails["cardholder_name"],
				"Authorization_Num"=>0,
				"Transaction_Tag"=>'none',
				"DollarAmount"=>$totals['total'],
				"VerificationStr1"=>'none',
				"Currency"=>"USD",
				"ZipCode"=>$postal_code,
				"Tax1Amount"=>$totals['taxes'],
				"Tax1Number"=>0,
				"Tax2Amount"=>0,
				"Tax2Number"=>0,
				"Track1"=>"",
				"Track2"=>"",
				"VerificationStr2"=>$creditCardDetails["cvv2"],
				"CVD_Presence_Ind"=>"",
				"Secure_AuthRequired"=>"",
				"Secure_AuthResult"=>"",
				"Ecommerce_Flag"=>"2",
				"XID"=>"",
				"CAVV"=>"",
				"CAVV_Algorithm"=>"",
				"PartialRedemption"=>"",
				"SurchargeAmount"=>0,	//Used for debit transactions only
				"PAN"=>0 				//Used for debit transactions only
		);
	}

	public function paymentProcess(){

		$this->response = $this->soap_api->SendAndCommit($this->request);

		if($this->isTransactionApproved()) {
			return self::PAYMENT_OK;
		}
		else {
			return self::PAYMENT_ERROR;
		}
	}

	public function getErrorMessage(){
		if ($this->response->Bank_Message){
			return $this->response->Bank_Message;
		} else {
			return $this->response->EXact_Message;
		}
	}

	public function isTransactionApproved(){
		if($this->response->Transaction_Approved){
			return true;
		} else {
			return false;
		}
	}

	public function getTransactionDetails(){
			$data = array(
					'message' =>$this->response->EXact_Message,
					'status' => TransactionDetails::TRANSACTION_STATUS_SUCCESS,
					'timestamp' => new \DateTime(),
					'reference' => $this->response->CTR
			);
			return new TransactionDetails($data);
	}

}