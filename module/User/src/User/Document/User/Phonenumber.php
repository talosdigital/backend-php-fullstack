<?php

namespace User\Document\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Phonenumber extends Document
{
	public function __construct($data = null)
    {
    	parent::__construct($data);
    }
	
	
    /** @ODM\String */
    protected $phonenumber;
	


}
