<?php

namespace User\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use MyZend\Document\Document as Document;

/** @ODM\Document(collection="user_user") */
class User extends Document implements RoleProviderInterface
{
	
	public function __construct($data = null) {
		parent::__construct($data);
		$this->addresses = new ArrayCollection();
		$this->phonenumbers = new ArrayCollection();
		$this->validation = new ArrayCollection();
        $this->oauth = new ArrayCollection();
	}
	
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

	/** @ODM\EmbedMany(targetDocument="User\Entity\User\Address") */
	protected $addresses = array();

	/** @ODM\EmbedMany(targetDocument="User\Entity\User\Phonenumber") */
	protected $phonenumbers = array();

	/** @ODM\EmbedMany(targetDocument="User\Entity\User\Oauth\Oauth") */
	protected $oauth = array();

	/** @ODM\EmbedMany(targetDocument="User\Entity\User\Validation", strategy="set") */
	protected $validation = array();


	public function getRoles() {
		
	}

    public function getOauthAdapter($adapter){
        $OauthTmp = $this->getOauth();
        foreach ($OauthTmp as $Oauth) {
            $adapterTmp = $Oauth->getAdapter();
            if($adapterTmp==$adapter){
                return $Oauth;
            }
        }
        return null;
    }
}
