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
 * Time: 22:18
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="project__project")
 * @ApiResource(
 *     itemOperations={
 *         "get"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Project not found."},
 *         "put"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Project not found."},
 *         "delete"={"access_control"="object.isAssociatedToProject(user) and is_granted('ROLE_ADMIN')", "access_control_message"="Project not found."}
 *     },
 *     attributes={
 *         "normalization_context"={"groups"={"read"}},
 *         "denormalization_context"={"groups"={"write"}}
 *     }
 * )
 */
class Project
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="id", nullable=false)
     * @Groups({"read", "write"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name", nullable=false)
     * @Groups({"read", "write"})
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Domain", mappedBy="project", cascade={"all"}, orphanRemoval=true)
     * @Groups({"read", "write"})
     */
    protected $domains;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinTable(name="project__users")
     */
    protected $users;

    public function __construct()
    {
        $this->domains = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        if (null === $this->name) {
            return "n/a";
        }

        return $this->getName();
    }

    public function isAssociatedToProject(User $user)
    {
        foreach ($this->users as $u) {
            if ($user === $u) {
                return true;
            }
        }

        return false;
    }

    public function getDomainByName($name)
    {
        $criteria = Criteria::create()->andWhere(Criteria::expr()->eq('name', $name))->setMaxResults(1);

        return $this->domains->matching($criteria)->first();
    }

    /**
     * @return int
     */
    public function countAssets()
    {
        $count = 0;

        /** @var Domain $domain */
        foreach ($this->domains as $domain) {
            $count += count($domain->getAssets());
        }

        return $count;
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
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDomains(): \Traversable
    {
        return $this->domains;
    }

    /**
     * @param ArrayCollection $domains
     * @return Project
     */
    public function setDomains($domains)
    {
        foreach ($this->domains as $domain) {
            $this->removeDomain($domain);
        }

        $this->domains = new ArrayCollection();

        foreach ($domains as $domain) {
            $this->addDomain($domain);
        }

        return $this;
    }

    /**
     * @param Domain $domain
     */
    public function addDomain(Domain $domain)
    {
        if (!$this->domains->contains($domain)) {
            $domain->setProject($this);
            $this->domains->add($domain);
        }
    }

    /**
     * @param Domain $domain
     */
    public function removeDomain(Domain $domain)
    {
        if ($this->domains->contains($domain)) {
            $domain->setProject(null);
            $this->domains->removeElement($domain);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers(): \Traversable
    {
        return $this->users;
    }

    /**
     * @param ArrayCollection $users
     * @return Project
     */
    public function setUsers($users)
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }

        return $this;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
    }
}
