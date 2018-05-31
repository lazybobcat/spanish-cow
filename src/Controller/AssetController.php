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
 * Date: 31/05/18
 * Time: 14:38
 */

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Project;
use App\Form\AssetType;
use App\Manager\AssetManager;
use App\Voter\ProjectVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AssetController extends Controller
{
    /**
     * @Route("/project/{project}/domain/{domain}/asset/add", name="asset_add")
     */
    public function add(Request $request, AssetManager $assetManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project, Domain $domain)
    {
        if (!$this->isGranted(ProjectVoter::UPDATE, $project) || $domain->getProject() !== $project) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem('breadcrumbs.domain_add', $router->generate('domain_add', ['project' => $project->getId()]));

        /** @var Asset $asset */
        $asset = $assetManager->create();
        $asset->setDomain($domain);
        $form = $this->createForm(AssetType::class, $asset);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $assetManager->save($asset);

            return $this->redirectToRoute('translate', ['project' => $project->getId(), 'domain' => $domain->getId()]);
        }

        return $this->render('assets/edit.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'form' => $form->createView(),
        ]);
    }
}
