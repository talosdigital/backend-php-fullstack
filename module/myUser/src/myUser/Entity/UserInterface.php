<?php

namespace myUser\Entity;

interface UserInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $id
     * @return UserInterface
     */
    public function setId($id);

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Set username.
     *
     * @param string $username
     * @return UserInterface
     */
    public function setUsername($username);

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set email.
     *
     * @param string $email
     * @return UserInterface
     */
    public function setEmail($email);

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set Name.
     *
     * @param string $name
     * @return UserInterface
     */
    public function setName($name);

    /**
     * Get password.
     *
     * @return string password
     */
    public function getPassword();

    /**
     * Set password.
     *
     * @param string $password
     * @return UserInterface
     */
    public function setPassword($password);

    /**
     * Get role.
     *
     * @return string
     */
    public function getRole();

    /**
     * Set role.
     *
     * @param string $role
     * @return UserInterface
     */
    public function setRole($role);
}
