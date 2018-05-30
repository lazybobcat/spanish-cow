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
 * Time: 14:20
 */

namespace App\Controller;

use App\Entity\Domain;
use App\Entity\Project;
use App\Manager\AssetManager;
use App\Voter\ProjectVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class TranslationController extends Controller
{
    /**
     * @Route("/project/{project}/domain/{domain}/translate", name="translate")
     */
    public function translate(Request $request, AssetManager $assetManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project, Domain $domain)
    {
        if (!$this->isGranted(ProjectVoter::UPDATE, $project)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName(), $router->generate('translate', ['project' => $project->getId(), 'domain' => $domain->getId()]));

        $assets = $assetManager->findBy(['domain' => $domain], ['resname' => 'ASC']);

        return $this->render('translation/translate.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'assets' => $assets,
        ]);
    }
}