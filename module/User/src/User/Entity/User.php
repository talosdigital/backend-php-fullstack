<?php

namespace User\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MyZend\Document\Document as Document;


/** @ODM\Document(collection="user_user") */
class User extends Document
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

}
