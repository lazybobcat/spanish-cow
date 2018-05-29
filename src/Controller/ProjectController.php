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
 * Time: 10:14
 */

namespace App\Controller;

use App\Manager\ProjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class ProjectController extends Controller
{
    const MAX_PER_PAGE = 10;

    /**
     * @Route("/projects", name="project_list")
     */
    public function listing(Request $request, ProjectManager $projectManager, Breadcrumbs $breadcrumbs, RouterInterface $router)
    {
        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));

        $page = $request->get('page', 1);
        $projects = $projectManager->findForUser($this->getUser(), $page, self::MAX_PER_PAGE);

        return $this->render('projects/listing.html.twig', [
            'projects' => $projects,
        ]);
    }
}
