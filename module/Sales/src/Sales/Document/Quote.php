<?php
namespace Sales\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;


use MyZend\Document\Document as Document;

/** @ODM\Document(collection="sales_quote") */
class Quote extends Document {

	public function __construct($data = null)
	{
		parent::__construct($data);

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
	 *
	 * @var User
	 *
	 *@ODM\ReferenceOne(targetDocument="User\Entity\User")
	 */
	protected $user;

	/**
	 * Email
	 * @var String
	 *
	 * @ODM\String
	 *  */
	protected $email;

	/**
	 *
	 * @var Quote Items
	 *
	 * @ODM\EmbedMany(targetDocument="Sales\Document\Item")
	 * */
	protected $items = array();

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

	public function addItem($item) {
		$this->getItems()->add($item);
	}
}
















