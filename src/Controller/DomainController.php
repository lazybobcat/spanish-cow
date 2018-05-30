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
 * Date: 29/05/18
 * Time: 13:40
 */

namespace App\Controller;

use App\Entity\Project;
use App\Manager\DomainManager;
use App\Voter\ProjectVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class DomainController extends Controller
{
    const MAX_PER_PAGE = 10;

    /**
     * @Route("/project/{project}/domains", name="domain_list")
     */
    public function listing(Request $request, DomainManager $domainManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project)
    {
        if (!$this->isGranted(ProjectVoter::READ, $project)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));

        $page = $request->get('page', 1);
        $domains = $domainManager->findByProject($project, $page, self::MAX_PER_PAGE);
        $progress = [];

        foreach ($domains as $domain) {
            $progress[$domain->getId()] = $domainManager->findTranslationProgress($domain);
        }

        return $this->render('domains/listing.html.twig', [
            'project' => $project,
            'domains' => $domains,
            'progress' => $progress,
        ]);
    }
}