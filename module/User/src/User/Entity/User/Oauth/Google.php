<?php
	
namespace User\Entity\User\Oauth;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Google extends AbstractOauth implements IOauth{

}