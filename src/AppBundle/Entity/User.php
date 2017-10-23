<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\UserRepository")
 * @ORM\Table(name="sl_user", uniqueConstraints={
 *   @ORM\UniqueConstraint("users_username_unique", columns="username"),
 *   @ORM\UniqueConstraint("users_email_address_unique", columns="email_address")
 * })
 *
 * @UniqueEntity("username", groups="Signup")
 * @UniqueEntity("emailAddress")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="smallint", options={ "unsigned": true })
     */
    private $id;

    /**
     * @ORM\Column(length=50)
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=50)
     */
    private $fullName;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $emailAddress;

    /**
     * @ORM\Column(length=25)
     *
     * @Assert\NotBlank(groups="Signup")
     * @Assert\Length(min=5, max=25, groups="Signup")
     * @Assert\Regex(
     *   pattern="/^[a-z0-9_]+$/i",
     *   groups="Signup",
     *   message="user.username.invalid_format"
     * )
     * @Assert\NotIdenticalTo("admin", groups="Signup")
     * @Assert\NotIdenticalTo("superadmin", groups="Signup")
     * @Assert\NotIdenticalTo("root", groups="Signup")
     * @Assert\NotIdenticalTo("_exit", groups="Signup")
     */
    private $username;

    /**
     * @ORM\Column
     *
     * @Assert\NotBlank(groups="Signup")
     * @Assert\Length(min=8)
     */
    private $password;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\NotBlank(groups="Signup")
     * @Assert\Range(max="-18 years")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $permissions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoggedAt;

    public function recordLastLoginTime()
    {
        $this->lastLoggedAt = new \DateTime();
    }

    /**
     * @Assert\Callback
     */
    public function checkPlainPassword(ExecutionContextInterface $context)
    {
        if (false !== stripos($this->password, $this->username)) {
            $context
                ->buildViolation('user.password.username_detected')
                ->setParameter('{{ username }}', $this->username)
                ->setParameter('{{ password }}', $this->password)
                ->atPath('password')
                ->addViolation()
            ;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public static function create($fullName, $birthdate)
    {
        return new self($fullName, $birthdate);
    }

    public function __construct($fullName = null, $birthdate = null)
    {
        $this->permissions = [];

        $this->fullName = $fullName;

        if (!$birthdate instanceof \DateTime) {
            $birthdate = new \DateTime($birthdate);
        }

        $this->birthdate = $birthdate;
        $this->grant('ROLE_PLAYER');
        $this->createdAt = new \DateTime('now');
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @Assert\IsTrue(
     *   message="user.birthdate.must_be_adult",
     *   groups="Signup"
     * )
     */
    public function isAdult()
    {
        return $this->birthdate <= new \DateTime('-18 years');
    }

    public function grant($permissions)
    {
        if (!is_array($permissions)) {
            $permissions = array($permissions);
        }

        foreach ($permissions as $permission) {
            if (!in_array($permission, $this->permissions)) {
                $this->permissions[] = $permission;
            }
        }
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getBirthdate()
    {
        return $this->birthdate;
    }

    public function getRoles()
    {
        $roles = [];
        foreach ($this->permissions as $permission) {
            $roles[] = new Role($permission);
        }

        return $roles;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
