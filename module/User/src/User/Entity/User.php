<?php

namespace User\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/** @ODM\Document(collection="user") */
class User implements UserInterface
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

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username.
     *
     * @param string $username
     * @return UserInterface
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     * @return UserInterface
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name.
     *
     * @param string $name
     * @return UserInterface
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get Role.
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set Role.
     *
     * @param int $role
     * @return UserInterface
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }


    /**
     * Get Facebook.
     *
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set Facebook.
     *
     * @param string $role
     * @return UserInterface
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
        return $this;
    }
}
