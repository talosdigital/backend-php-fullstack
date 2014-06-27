<?php

namespace Sales\Document\Order;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\EmbeddedDocument */
class TransactionDetails extends Document
{
	public function __construct($data = null)
	{
		parent::__construct($data);
	}

	/**
	 * Transaction timestamp
	 * @var DateTime
	 *
	 *  @ODM\Date
	 */
	protected $timestamp;

	/**
	 * Transaction reference
	 * @var String
	 *
	 *  @ODM\String
	 */
	protected $reference;

	/**
	 * Transaction description
	 * @var String
	 *
	 *  @ODM\String
	 */
	protected $message;

	/**
	 * Error message
	 * @var String
	 *
	 *  @ODM\String
	 */
	protected $error_message;

	/**
	 * Transaction status
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $status;
	const TRANSACTION_STATUS_SUCCESS = "Success";
	const TRANSACTION_STATUS_FAILURE = "Failure";
	
	/**
	 * ID of recurring user PayPal profile
	 * @var String
	 *
	 *  @ODM\String
	 */
	protected $profile_id;


}


