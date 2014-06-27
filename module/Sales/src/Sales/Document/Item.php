<?php
namespace Sales\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use MyZend\Document\Document as Document;

/** @ODM\EmbeddedDocument */
class Item extends Document
{
	public function __construct($data = null)
	{
		parent::__construct($data);
	}

	/**
	 * Stock keeping unit
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $sku;

	/**
	 * Item name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $name;

	/**
	 * Quantity of item
	 * @var Integer
	 *
	 * @ODM\Int
	 */
	protected $quantity;

	/**
	 * Property of item
	 * @var Integer
	 *
	 * @ODM\Int
	 */
	protected $property;

	/**
	 * Price of item
	 * @var Float
	 *
	 * @ODM\Float
	 */
	protected $price;

	/**
	 * Tax of item
	 * @var Float
	 *
	 * @ODM\Float
	 */
	protected $tax;


}