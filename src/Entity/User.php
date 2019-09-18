<?php

// src/Entity/User.php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * Our general purpose user class.
 *
 * @package App\Entity
 *
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=254, unique=true)
     *
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     *
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var string
     *
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Paper", mappedBy="user", orphanRemoval=true)
     */
    private $papers;

    /**
     * User constructor.
     *
     * Sets up a new active user with a default role.
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = ['ROLE_USER'];
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->papers = new ArrayCollection();
    }

    /**
     * Returns the current username.
     *
     * @return string|null
     *
     * @see UserInterface::getUsername()
     */
    public function getUsername() : ?string
    {
        return $this->username;
    }

    /**
     * Returns the salt.
     *
     * Salt isn't required for the auth system
     * we're currently using therefore returning null.
     *
     * @return string|null
     * @see UserInterface::getSalt()
     */
    public function getSalt() : ?string
    {
        return null;
    }

    /**
     * Returns the encrypted password.
     *
     * @return string|null
     *
     * @see UserInterface::getPassword()
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * Returns an array of roles for the user,
     *
     * @return array
     *
     * @see UserInterface::getRoles()
     */
    public function getRoles() : array
    {
        return $this->roles;
    }

    /**
     * Sets the roles.
     *
     * This isn't an append operation. It will
     * replace all of the roles with the supplied
     * array.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles) : self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Can be used to erase sensitive user data.
     *
     * Currently unused.
     *
     * @return bool
     *
     * @see UserInterface::eraseCredentials()
     */
    public function eraseCredentials() : bool
    {
        //TODO: Implement!
        return false;
    }

    /**
     * Serialize the user object into a string.
     *
     * @return string
     *
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * Populate the user object with serialized data.
     *
     * @param string $serialized
     *
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * Returns the user ID.
     *
     * @return mixed
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * Sets the username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username) : self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Sets the password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password) : self
    {
        if (!is_null($password)) {
            $this->password = $password;
        }

        return $this;
    }

    /**
     * Get the email.
     *
     * @return string|null
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Set the email address.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get active status.
     *
     * @return bool|null
     */
    public function getIsActive() : ?bool
    {
        return $this->isActive;
    }

    /**
     * Set the active status.
     *
     * @param bool $isActive
     *
     * @return User
     */
    public function setIsActive(bool $isActive) : self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the plain text password.
     *
     * @return mixed
     */
    public function getPlainPassword() : ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set the plain password.
     *
     * @param string $password
     */
    public function setPlainPassword(string $password)
    {
        $this->plainPassword = $password;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection|Paper[]
     */
    public function getPapers(): Collection
    {
        return $this->papers;
    }

    public function addPaper(Paper $paper): self
    {
        if (!$this->papers->contains($paper)) {
            $this->papers[] = $paper;
            $paper->setUser($this);
        }

        return $this;
    }

    public function removePaper(Paper $paper): self
    {
        if ($this->papers->contains($paper)) {
            $this->papers->removeElement($paper);
            // set the owning side to null (unless already changed)
            if ($paper->getUser() === $this) {
                $paper->setUser(null);
            }
        }

        return $this;
    }
}
