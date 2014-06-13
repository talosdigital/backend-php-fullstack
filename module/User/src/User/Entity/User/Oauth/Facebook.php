<?php
	
namespace User\Entity\User\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Facebook extends Oauth implements IOauth{
	const ADAPTER = "facebook";

	function __construct(){
		$this->setAdapter($this::ADAPTER);
	}

}