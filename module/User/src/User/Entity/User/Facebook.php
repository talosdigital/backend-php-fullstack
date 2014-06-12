<?php

namespace User\Entity\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Facebook extends Document
{
    /** @ODM\String */
    protected $facebook_id;

    /** @ODM\String */
    protected $username;

    /** @ODM\String */
    protected $email;

    /** @ODM\String */
    protected $picture;

    /*public function toArray(){
    	$array = array(
    		'facebook_id' => $this->getFacebookId(),
    		'username' => $this->getUsername(),
    		'email' => $this->getEmail(),
    		'picture' => $this->getPicture()
    		);
    	return $array;
    }*/ 
}
