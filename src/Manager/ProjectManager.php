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
 * Time: 12:19
 */

namespace App\Manager;

use App\Entity\User;
use App\Repository\ProjectRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @method ProjectRepository getRepository()
 */
class ProjectManager extends BaseManager
{
    /**
     * @param User $user
     * @param int  $page
     * @param int  $maxPerPage
     *
     * @return Pagerfanta
     */
    public function findForUser(User $user, $page = 1, $maxPerPage = 25)
    {
        $qb = $this->getRepository()->findForUserQueryBuilder($user);
        $adapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($adapter);
        $pager
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($page)
        ;

        return $pager;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getRepository()->getQueryBuilder();
    }
}
