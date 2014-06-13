<?php 

namespace User\Entity\User;

use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Oauth extends Document{

	/** @ODM\EmbedOne(targetDocument="User\Entity\User\Oauth\Facebook") */
    protected $facebook;

    /** @ODM\EmbedOne(targetDocument="User\Entity\User\Oauth\Twitter") */
    protected $twitter;

    /** @ODM\EmbedOne(targetDocument="User\Entity\User\Oauth\Google") */
    protected $google;

    public function removeFacebook(){
    	$this->facebook = null;
    }

    public function removeTwitter(){
    	$this->twitter = null;
    }

    public function removeGoogle(){
    	$this->google = null;
    }

}