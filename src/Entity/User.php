<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;

    public function getSalt()
    {
        return null;
    }

    public function getPassword()
    {
        return null;
    }

    public function eraseCredentials()
    {
        // empty
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username
        ) = unserialize($serialized);
    }
}
