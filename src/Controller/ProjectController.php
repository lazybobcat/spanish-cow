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

use App\Entity\Project;
use App\Form\ProjectType;
use App\Manager\ProjectManager;
use App\Voter\ProjectVoter;
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

    /**
     * @Route("/project/add", name="project_add")
     */
    public function add(Request $request, ProjectManager $projectManager, Breadcrumbs $breadcrumbs, RouterInterface $router)
    {
        /** @var Project $project */
        $project = $projectManager->create();

        if (!$this->isGranted(ProjectVoter::CREATE, $project)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem('breadcrumbs.project_add', $router->generate('project_add'));

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $project->addUser($this->getUser());
            $projectManager->save($project);

            return $this->redirectToRoute('domain_list', ['project' => $project->getId()]);
        }

        return $this->render('projects/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/delete", name="project_delete")
     */
    public function delete(Request $request, ProjectManager $projectManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project)
    {
        if (!$this->isGranted(ProjectVoter::DELETE, $project)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName());

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $projectManager->delete($project);

            return $this->redirectToRoute('project_list');
        }

        return $this->render('projects/delete.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }
}
