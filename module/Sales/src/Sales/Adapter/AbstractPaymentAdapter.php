<?php
namespace Sales\Adapter;

abstract class AbstractPaymentAdapter {

	protected $request;
	const TRANSACTION_TYPE_PURCHASE = "purchase";
	const TRANSACTION_TYPE_AUTHORIZATION = "authorization";
	const TRANSACTION_TYPE_RECURRING = "recurring";
	
	protected $status;
	const RESPONSE_OK = "Ok";
	const RESPONSE_ERROR = "Error";
	
	protected $recurring_status;
	const RECURRING_STATUS_ACTIVE = "active";
	const RECURRING_STATUS_CANCELLLED = "cancelled";
	const RECURRING_STATUS_EXPIRED = "expired";

}