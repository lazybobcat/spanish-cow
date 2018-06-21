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
 * Date: 11/06/18
 * Time: 15:10
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\API\DomainAPIImport;

/**
 * @ApiResource(
 *     itemOperations={},
 *     collectionOperations={
 *         "import"={
 *             "path"="/{project}/{domain}/{locale}/import", "method"="POST", "controller"=DomainAPIImport::class,
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
 *                         "name" = "locale",
 *                         "in" = "path",
 *                         "required" = "true",
 *                         "type" = "string"
 *                     },
 *                     {
 *                         "name" = "import",
 *                         "in" = "body",
 *                         "required" = "true",
 *                         "schema" = {"$ref"="#/definitions/Import"}
 *                     }
 *                 }
 *             }
 *         }
 *     }
 * )
 */
class Import
{
    /**
     * @var string
     */
    protected $xliff;

    /**
     * @return string
     */
    public function getXliff(): ?string
    {
        return $this->xliff;
    }

    /**
     * @param string $xliff
     *
     * @return Import
     */
    public function setXliff($xliff)
    {
        $this->xliff = $xliff;

        return $this;
    }
}
