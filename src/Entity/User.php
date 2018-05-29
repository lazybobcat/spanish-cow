<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 24/05/18
 * Time: 16:28
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\Table(name="user__user")
 */
class User implements UserInterface, \Serializable
{
    use Timestampable;

    const ROLE_DEFAULT = 'ROLE_USER';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="id", nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="email", nullable=false, unique=true, length=190)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="password", nullable=false)
     */
    protected $password;

    /**
     */
    protected $plainPassword;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="active")
     */
    protected $active = true;

    /**
     * @var array
     *
     * @ORM\Column(type="array", name="roles", nullable=false)
     */
    protected $roles = [self::ROLE_DEFAULT];

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="users")
     */
    protected $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole($role)
    {
        if (is_string($role)) {
            $this->roles[] = $role;
        } elseif (is_array($role)) {
            $this->roles = array_merge($this->roles, $role);
        }
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @param $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->setEmail($username);

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}