<?php
namespace Sales\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\Document(collection="sales_order") */
class Order extends Document {

	public function __construct($data = null)
	{
		parent::__construct($data = null);
		$this->items = new ArrayCollection();
	}

	/**
	 * Id
	 * @var MongoId
	 *
	 * @ODM\Id
	 */
	protected $id;

	/**
	 * User
	 * @var User
	 *
	 *@ODM\ReferenceOne(targetDocument="User\Entity\User")
	 */
	protected $user;

	/**
	 * E-mail
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $email;

	/**
	 *
	 * @var Order Items
	 *
	 * @ODM\EmbedMany(targetDocument="Sales\Document\Item")
	 * */
	protected $items = array();

	/**
	 *
	 * @var Voucher
	 *
	 * @ODM\Hash
	 * */
	protected $voucher;


	/**
	 * An array of totals
	 * @var Array
	 *
     * @ODM\Hash
	 * */
	protected $totals = array();

	/**
	 *
	 * @var Billing details
	 *
	 * @ODM\EmbedOne(targetDocument="User\Entity\User\Address")
	 * */
	protected $billing_details;
	
	/**
	 *
	 * @var Phone Number
	 *
	 * @ODM\EmbedOne(targetDocument="User\Entity\User\Phonenumber")
	 * */
	protected $phonenumber;

	/**
	 *
	 * @var Transaction details
	 *
	 * @ODM\EmbedOne(targetDocument="Sales\Document\Order\TransactionDetails")
	 * */
	protected $transaction_details;

	/**
	 * Order status
	 * @var String
	 *
	 *  @ODM\String
	 */
	protected $status;
	const ORDER_STATUS_PAID = "paid";
	const ORDER_STATUS_CANCELED = "canceled";

	/**
	 * Created Date
	 * @var DateTime
	 *
	 *  @ODM\Date
	 */
	 protected $created_at;

	/** @ODM\PrePersist */
	public function prePersist()
	{
		$this->created_at = new \DateTime();
	}
	
	public function getInvoiceId() {
		$length = strlen($this->getId());
		return strtoupper(substr($this->getId(), $length - 8, 8));
	}
	
	public function cloneMe() {
		$cloned = clone $this;
		$cloned->setId(null);
		$cloned->setItems($this->getItems()->toArray());
		
		return $cloned;
	}
	
}