<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loic
 * Date: 03/04/18
 * Time: 22:30
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LocaleRepository")
 * @ORM\Table(name="locale__locale")
 * @ApiResource(
 *     itemOperations={
 *         "get"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "put"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "delete"={"access_control"="object.isAssociatedToProject(user) and is_granted('ROLE_ADMIN')", "access_control_message"="Locale not found."}
 *     }
 * )
 */
class Locale
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="id", nullable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name", nullable=false)
     */
    protected $name;

    /**
     * ISO 639-1 code.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=2, name="code", nullable=false)
     */
    protected $code;

    /**
     * @var Domain
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Domain", inversedBy="locales")
     * @ORM\JoinColumn(name="domain_id", referencedColumnName="id")
     */
    protected $domain;

    public function __toString()
    {
        return $this->getCode();
    }

    public function isAssociatedToProject(User $user)
    {
        if (!$this->getDomain()) {
            return false;
        }

        return $this->getDomain()->isAssociatedToProject($user);
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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Locale
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Locale
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Domain
     */
    public function getDomain(): ?Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     *
     * @return Locale
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }
}
