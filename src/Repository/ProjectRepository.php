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
 * Time: 12:20
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param int $start
     * @param int $limit
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findForUserQueryBuilder(User $user)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->andWhere(':user MEMBER OF p.users')
            ->setParameter('user', $user)
        ;

        return $qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->createQueryBuilder('p');
    }
}
