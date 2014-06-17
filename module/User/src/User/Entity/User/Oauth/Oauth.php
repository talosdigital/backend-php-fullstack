<?php 

namespace User\Entity\User\Oauth;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Oauth extends Document{

	/** @ODM\String */
    protected $adapter;

	/** @ODM\String */
    protected $id;

    /** @ODM\String */
    protected $email;

    /** @ODM\String */
    protected $picture;

}