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

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Locale;
use App\Entity\Project;
use App\Entity\Translation;
use App\Form\AssetType;
use App\Manager\AssetManager;
use App\Manager\TranslationManager;
use App\Voter\ProjectVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        if (!$this->isGranted(ProjectVoter::UPDATE, $project) || $domain->getProject() !== $project) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName(), $router->generate('translate', ['project' => $project->getId(), 'domain' => $domain->getId()]));

        $assets = $assetManager->findBy(['domain' => $domain], ['resname' => 'ASC']);
        $select = $request->get('active', null);

        $form = $this->createForm(AssetType::class, new Asset(), ['csrf_protection' => false]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Asset $asset */
            foreach ($assets as $asset) {
                if ($asset->getId() == $form->getData()->getId()) {
                    $asset->setNotes($form->getData()->getNotes());
                    $assetManager->save($asset);
                    $select = $asset->getId();
                    break;
                }
            }

            return $this->redirectToRoute('translate', [
                'project' => $project->getId(),
                'domain' => $domain->getId(),
                'active' => $select,
            ]);
        }

        return $this->render('translation/translate.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'assets' => $assets,
            'select' => $select,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/ajax-translate", name="ajax_translate", condition="request.isXmlHttpRequest()", methods={"POST"})
     */
    public function ajaxTranslate(Request $request, AssetManager $assetManager, TranslationManager $translationManager, Project $project, Domain $domain)
    {
        if (!$this->isGranted(ProjectVoter::UPDATE, $project) || $domain->getProject() !== $project) {
            return new JsonResponse(['message' => 'Project not found'], 404);
        }

        /** @var Asset $asset */
        $asset = $assetManager->find($request->request->get('id'));
        $translations = $request->request->get('translation', []);
        if (null === $asset || $asset->getDomain() !== $domain) {
            return new JsonResponse(['message' => 'Asset not found'], 404);
        }

        /** @var Locale $locale */
        foreach ($domain->getLocales() as $locale) {
            if (isset($translations[$locale->getCode()])) {
                $translation = $asset->getTranslationForCode($locale->getCode());
                if (!$translation) {
                    $translation = new Translation();
                    $translation
                        ->setAsset($asset)
                        ->setLocale($locale)
                    ;
                }

                $translation->setTarget($translations[$locale->getCode()]);
                $translationManager->save($translation);
            }
        }

        return new JsonResponse(['message' => 'Ok'], 200);
    }
}
