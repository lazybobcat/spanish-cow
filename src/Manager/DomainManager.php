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

use App\Entity\Domain;
use App\Entity\Project;
use App\Repository\DomainRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @method DomainRepository getRepository()
 */
class DomainManager extends BaseManager
{
    /**
     * @param Project $project
     * @param int     $page
     * @param int     $maxPerPage
     *
     * @return Pagerfanta
     */
    public function findByProject(Project $project, $page = 1, $maxPerPage = 25)
    {
        $qb = $this->getRepository()->findForProjectQueryBuilder($project);
        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($page)
        ;

        return $pager;
    }

    public function findTranslationProgress(Domain $domain)
    {
        try {
            $prct = $this->getRepository()->findTranslationProgressScalar($domain);
        } catch (\Exception $e) {
            return 0;
        }

        return $prct;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->getQueryBuilder();
    }
}
