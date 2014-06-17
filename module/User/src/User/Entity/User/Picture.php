<?php

namespace User\Entity\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Picture extends Document
{
	/**
     * Id
     * @var MongoId
     *
     * @ODM\Id
     */
	protected $id;

	/** @ODM\String */
	protected $url;

	/** @ODM\String */
	protected $long_url;

	/** @ODM\String */
	protected $size;

	/** @ODM\String */
	protected $type;
}