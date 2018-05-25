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
 * Time: 15:28
 */

namespace App\Manager;

use App\Repository\DomainRepository;

/**
 * @method DomainRepository getRepository()
 */
class DomainManager extends BaseManager
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->getQueryBuilder();
    }
}
