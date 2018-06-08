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
 * Time: 23:08
 */

namespace App\Entity;

use App\Controller\API\TranslationAPIPost;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TranslationRepository")
 * @ORM\Table(name="asset__translation")
 * @ApiResource(
 *     itemOperations={
 *         "get"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "put"={"access_control"="object.isAssociatedToProject(user)", "access_control_message"="Domain not found."},
 *         "delete"={"access_control"="object.isAssociatedToProject(user) and is_granted('ROLE_ADMIN')", "access_control_message"="Translation not found."}
 *     },
 *     collectionOperations={
 *         "postcustom"={
 *             "path"="/{project}/{domain}/{resname}/{locale}/translations", "method"="POST", "controller"=TranslationAPIPost::class,
 *             "swagger_context" = {
 *                 "parameters" = {
 *                     {
 *                         "name" = "project",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "integer"
 *                     },
 *                     {
 *                         "name" = "domain",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "string"
 *                     },
 *                     {
 *                         "name" = "resname",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "string"
 *                     },
 *                     {
 *                         "name" = "locale",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "string"
 *                     },
 *                     {
 *                         "name" = "asset",
 *                         "in" = "body",
 *                         "required" = "true",
 *                         "schema" = {"$ref"="#/definitions/Translation"}
 *                     }
 *                 }
 *             }
 *         }
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"asset.domain": "exact", "asset.domain.name": "exact", "asset.domain.project": "exact", "asset.resname": "exact", "locale.code": "exact"})
 */
class Translation
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
     * @var Asset
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Asset", inversedBy="translations")
     * @ORM\JoinColumn(name="asset_id", referencedColumnName="id")
     */
    protected $asset;

    /**
     * @var Locale
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    protected $locale;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="target", nullable=true)
     */
    protected $target;

    public function isAssociatedToProject(User $user)
    {
        if (!$this->getAsset()) {
            return false;
        }

        return $this->getAsset()->isAssociatedToProject($user);
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Asset
     */
    public function getAsset(): ?Asset
    {
        return $this->asset;
    }

    /**
     * @param Asset $asset
     *
     * @return Translation
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * @return Locale
     */
    public function getLocale(): ?Locale
    {
        return $this->locale;
    }

    /**
     * @param Locale $locale
     *
     * @return Translation
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return Translation
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }
}
