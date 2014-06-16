<?php

namespace User\Entity\User;

use \Zend\Permissions\Acl\Role\RoleInterface;
use MyZend\Document\Document as Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\EmbeddedDocument */
class Role extends Document implements RoleInterface{
	
	 /**
     * Role
     * @var String
     *
     * @ODM\String
     */
	protected $role_id;

	public function getRoleId(){
		return $this->role_id;
	}

}