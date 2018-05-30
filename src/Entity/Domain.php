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
 * Time: 22:33
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 * @ORM\Table(name="project__domain")
 * @ApiResource(
 *     itemOperations={
 *         "get"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "put"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "delete"={"access_control"="object.isAssociatedToProject(user) and is_granted('ROLE_ADMIN')", "access_control_message"="Domain not found."}
 *     }
 * )
 */
class Domain
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="id", nullable=false)
     */
    protected $id;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="domains")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="name", nullable=false)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var Asset[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Asset", mappedBy="domain", cascade={"all"}, orphanRemoval=true)
     */
    protected $assets;

    /**
     * @var Locale[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Locale")
     * @ORM\JoinTable(name="project__domain_locales",
     *     joinColumns={@ORM\JoinColumn(name="domain_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="locale_id", referencedColumnName="id")})
     */
    protected $locales;

    public function __construct()
    {
        $this->assets = new ArrayCollection();
        $this->locales = new ArrayCollection();
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
        if (!$this->getProject()) {
            return false;
        }

        return $this->getProject()->isAssociatedToProject($user);
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
     * @return Domain
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Asset[]
     */
    public function getAssets(): \Traversable
    {
        return $this->assets;
    }

    /**
     * @param Asset[] $assets
     * @return Domain
     */
    public function setAssets($assets)
    {
        foreach ($this->assets as $asset) {
            $this->removeAsset($asset);
        }

        $this->assets = new ArrayCollection();

        foreach ($assets as $asset) {
            $this->addAsset($asset);
        }

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return Domain
     */
    public function addAsset(Asset $asset)
    {
        if (!$this->assets->contains($asset)) {
            $asset->setDomain($this);
            $this->assets->add($asset);
        }

        return $this;
    }

    /**
     * @param Asset $asset
     *
     * @return Domain
     */
    public function removeAsset(Asset $asset)
    {
        if ($this->assets->contains($asset)) {
            $asset->setDomain(null);
            $this->assets->removeElement($asset);
        }

        return $this;
    }

    /**
     * @return Locale[]
     */
    public function getLocales(): \Traversable
    {
        return $this->locales;
    }

    /**
     * @param Locale[] $locales
     *
     * @return Domain
     */
    public function setLocales($locales)
    {
        foreach ($this->locales as $locale) {
            $this->removeLocale($locale);
        }

        $this->locales = new ArrayCollection();

        foreach ($locales as $locale) {
            $this->addLocale($locale);
        }

        return $this;
    }

    /**
     * @param Locale $locale
     *
     * @return Domain
     */
    public function addLocale(Locale $locale)
    {
        if (!$this->locales->contains($locale)) {
            $this->locales->add($locale);
        }

        return $this;
    }

    /**
     * @param Locale $locale
     *
     * @return Domain
     */
    public function removeLocale(Locale $locale)
    {
        if ($this->locales->contains($locale)) {
            $this->locales->removeElement($locale);
        }

        return $this;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return Domain
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }
}
