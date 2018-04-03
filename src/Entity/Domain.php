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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="project__domain")
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
     * @var string
     *
     * @ORM\Column(type="string", name="name", nullable=false)
     */
    protected $name;

    /**
     * @var Locale[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Locale", mappedBy="domain", cascade={"all"})
     */
    protected $locales;

    /**
     * @var Locale
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Locale")
     * @ORM\JoinColumn(name="default_locale_id", referencedColumnName="id")
     */
    protected $defaultLocale;

    public function __construct()
    {
        $this->locales = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
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
     * @return Locale[]
     */
    public function getLocales(): ArrayCollection
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
            $locale->setDomain($this);
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
            $locale->setDomain(null);
            $this->locales->removeElement($locale);
        }

        return $this;
    }

    /**
     * @return Locale
     */
    public function getDefaultLocale(): Locale
    {
        return $this->defaultLocale;
    }

    /**
     * @param Locale $defaultLocale
     *
     * @return Domain
     */
    public function setDefaultLocale(Locale $defaultLocale)
    {
        if (!$this->locales->contains($defaultLocale)) {
            throw new \LogicException("The locale '{$defaultLocale->getCode()}' is not part of the domain locales, impossible to set it as defaut.");
        }

        $this->defaultLocale = $defaultLocale;

        return $this;
    }
}
