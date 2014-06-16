<?php

namespace User\Entity\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Picture extends Document
{
	/** @ODM\String */
	protected $url;

	/** @ODM\String */
	protected $long_url;

	/** @ODM\String */
	protected $width;

	/** @ODM\String */
	protected $height;

	/** @ODM\String */
	protected $type;
}