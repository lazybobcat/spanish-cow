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

use App\Consumer\Topics;
use App\Entity\Domain;
use App\Entity\Project;
use App\Form\DomainType;
use App\Form\ExportType;
use App\Form\ImportFileType;
use App\Importer\FileImporter;
use App\Manager\DomainManager;
use App\Model\FileType;
use App\Model\Import;
use App\Voter\DomainVoter;
use App\Voter\ProjectVoter;
use Enqueue\Client\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
        $breadcrumbs->addItem($project->getName());

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

    /**
     * @Route("/project/{project}/domain/add", name="domain_add")
     */
    public function add(Request $request, DomainManager $domainManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project)
    {
        /** @var Domain $domain */
        $domain = $domainManager->create();
        $domain->setProject($project);

        if (!$this->isGranted(DomainVoter::CREATE, $domain)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem('breadcrumbs.domain_add');

        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $domainManager->save($domain);

            return $this->redirectToRoute('domain_list', ['project' => $project->getId()]);
        }

        return $this->render('domains/edit.html.twig', [
            'project' => $project,
            'domain' => null,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/edit", name="domain_edit")
     */
    public function edit(Request $request, DomainManager $domainManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project, Domain $domain)
    {
        if (!$this->isGranted(DomainVoter::UPDATE, $domain)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName());

        $form = $this->createForm(DomainType::class, $domain);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $domainManager->save($domain);

            return $this->redirectToRoute('domain_list', ['project' => $project->getId()]);
        }

        return $this->render('domains/edit.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/delete", name="domain_delete")
     */
    public function delete(Request $request, DomainManager $domainManager, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project, Domain $domain)
    {
        if (!$this->isGranted(DomainVoter::DELETE, $domain)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName());

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $domainManager->delete($domain);

            return $this->redirectToRoute('domain_list', ['project' => $project->getId()]);
        }

        return $this->render('domains/delete.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/import", name="domain_import")
     */
    public function import(Request $request, ProducerInterface $producer, FileImporter $importer, Breadcrumbs $breadcrumbs, RouterInterface $router, Project $project, Domain $domain, LoggerInterface $logger)
    {
        if (!$this->isGranted(DomainVoter::UPDATE, $domain)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName());

        $data = new Import();
        $data
            ->setDomainId($domain->getId())
            ->setDomainName($domain->getName())
            ->setTargetType(FileType::FILE_TYPE_DATABASE)
        ;
        $form = $this->createForm(ImportFileType::class, $data, [
            'domain' => $domain,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('import_file')['file'];
            $destination = $this->getParameter('import_translation_folder').DIRECTORY_SEPARATOR.$domain->getId();
            $file = $uploadedFile->move($destination, $uploadedFile->getClientOriginalName());
            $data->setFile($file);

//            // Async treatment of the importation
//            $producer->sendEvent(Topics::TOPIC_FILE_IMPORT, json_encode($data));

            $fs = new Filesystem();

            try {
                $importer->import($data);
                $fs->remove($data->getSourceFilePath());
                $this->addFlash('success', 'flash.import_success');
            } catch (\Exception $e) {
                $this->addFlash('error', 'flash.import_failure');
                $logger->error('Error during import of translation file', [
                    'data' => $data,
                    'exception' => $e,
                ]);

                return self::REJECT;
            }

            return $this->redirectToRoute('domain_list', ['project' => $project->getId()]);
        }

        return $this->render('domains/import.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/export", name="domain_export")
     */
    public function export(Request $request, Breadcrumbs $breadcrumbs, RouterInterface $router, FileImporter $importer, SessionInterface $session, Project $project, Domain $domain)
    {
        if (!$this->isGranted(DomainVoter::READ, $domain)) {
            throw $this->createNotFoundException();
        }

        $breadcrumbs->addItem('breadcrumbs.projects_listing', $router->generate('project_list'));
        $breadcrumbs->addItem($project->getName(), $router->generate('domain_list', ['project' => $project->getId()]));
        $breadcrumbs->addItem($domain->getName());

        $data = new Import();
        $data
            ->setSourceType(FileType::FILE_TYPE_DATABASE)
            ->setDomainName($domain->getName())
            ->setDomainId($domain->getId())
        ;

        $form = $this->createForm(ExportType::class, $data, [
            'domain' => $domain,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $filename = $domain->getName().'.'.$data->getLocaleCode().'.'.$data->getTargetType();
            $destination = $this->getParameter('import_translation_folder').DIRECTORY_SEPARATOR.$domain->getId().DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$filename;

            $data->setTargetFilePath($destination);
            $importer->import($data);

            $session->set('export.data', json_encode($data));

            return $this->redirectToRoute('domain_export_download', [
                'project' => $project->getId(),
                'domain' => $domain->getId(),
            ]);
        }

        return $this->render('domains/export.html.twig', [
            'project' => $project,
            'domain' => $domain,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/project/{project}/domain/{domain}/download", name="domain_export_download")
     */
    public function exportDownload(Request $request, SessionInterface $session, Project $project, Domain $domain)
    {
        if (!$this->isGranted(DomainVoter::READ, $domain)) {
            throw $this->createNotFoundException();
        }

        $json = $session->get('export.data');
        if (null === $json) {
            throw $this->createNotFoundException();
        }

        $data = new Import(json_decode($json, true));

        $response = new BinaryFileResponse($data->getTargetFilePath());
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($data->getTargetFilePath()),
            iconv('UTF-8', 'ASCII//TRANSLIT', basename($data->getTargetFilePath()))
        );

        return $response;
    }
}
