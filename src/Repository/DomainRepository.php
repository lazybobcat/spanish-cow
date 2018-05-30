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

namespace App\Repository;

use App\Entity\Domain;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;

class DomainRepository extends EntityRepository
{
    /**
     * @param Project $project
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findForProjectQueryBuilder(Project $project)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->andWhere('d.project = :project')
            ->setParameter('project', $project)
        ;

        return $qb;
    }

    public function findTranslationProgressScalar(Domain $domain)
    {
        $qb = $this->getQueryBuilder();

        $qb
            ->select('COUNT(DISTINCT(t.id)) / (COUNT(DISTINCT(a.id)) * COUNT(DISTINCT(l.id))) AS prct')
            ->innerJoin('d.assets', 'a')
            ->innerJoin('a.translations', 't')
            ->innerJoin('d.locales', 'l')
            ->andWhere('d = :domain')
            ->setParameter('domain', $domain)
        ;

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->createQueryBuilder('d');
    }
}
