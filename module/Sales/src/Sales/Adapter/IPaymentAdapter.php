<?php
namespace Sales\Adapter;

interface IPaymentAdapter {

	public function getErrorMessage();

	public function getTransactionDetails();
	
	public function createRecurringPaymentsProfileRequest($quote, $token, $payerId);
	
	public function manageRecurringPaymentsProfileStatusRequest($profileId, $newStatus);
	
	public function getRecurringPaymentsProfileDetailsRequest($profileId);
	
	public function updateRecurringPaymentsProfileRequest($profileId, $data);
	
	
}