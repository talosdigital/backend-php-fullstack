<?php

namespace User\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document(db="talos", collection="users") */
class User
{
	/**
	 * Id
	 * @var MongoId
	 *
	 * @ODM\Id
	 */

	protected $id;

	/**
	 * Name
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $name;

	/**
	 * Email
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $email;

	/**
	 * Password
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $password;

	/**
	 * Role (guest, user, admin)
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $role;

	/**
	 * facebook
	 * @var String
	 *
	 * @ODM\String
	 */
	protected $facebook;


	public function setName($name){
		$this->name = $name;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function setPassword($password){
		$this->password = md5($password);
	}

	public function setFacebook($facebook){
		$this->facebook = $facebook;
	}

	public function setRole($role){
		$this->role = $role;
	}

	public function getName(){
		return $this->name;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getFacebook(){
		return $this->facebook;
	}

	public function getPassword(){
		return $this->password;
	}

	public function getRole(){
		return $this->role;
	}

}