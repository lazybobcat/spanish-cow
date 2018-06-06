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
 * Date: 25/05/18
 * Time: 16:32
 */

namespace App\Manager;

use App\Entity\Domain;
use App\Repository\AssetRepository;

/**
 * @method AssetRepository getRepository()
 */
class AssetManager extends BaseManager
{
    public function deleteForDomain(Domain $domain)
    {
        $assets = $this->findBy(['domain' => $domain]);
        foreach ($assets as $asset) {
            $this->delete($asset);
        }
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->getQueryBuilder();
    }
}
