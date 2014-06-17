<?php
	
namespace User\Entity\User\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Twitter extends AbstractOauth implements IOauth{

}