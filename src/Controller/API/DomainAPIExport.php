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
 * Date: 11/06/18
 * Time: 10:56
 */

namespace App\Controller\API;

use App\Entity\Domain;
use App\Entity\Project;
use App\Importer\FileImporter;
use App\Model\FileType;
use App\Model\Import;
use App\Voter\DomainVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainAPIExport extends Controller
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var FileImporter
     */
    private $importer;

    public function __construct(RequestStack $requestStack, FileImporter $importer)
    {
        $this->requestStack = $requestStack;
        $this->importer = $importer;
    }

    public function __invoke(Project $project, $domain, $locale)
    {
        /** @var Domain $domain */
        $domain = $project->getDomainByName($domain);

        if (!$domain || !$this->isGranted(DomainVoter::READ, $domain)) {
            throw $this->createNotFoundException();
        }

        $filename = $domain->getName().'.'.$locale.'.xlf';
        $destination = $this->getParameter('import_translation_folder').DIRECTORY_SEPARATOR.$domain->getId().DIRECTORY_SEPARATOR.'exports'.DIRECTORY_SEPARATOR.$filename;

        $data = new Import();
        $data
            ->setSourceType(FileType::FILE_TYPE_DATABASE)
            ->setLocaleCode($locale)
            ->setDomainName($domain->getName())
            ->setDomainId($domain->getId())
            ->setTargetType(FileType::FILE_TYPE_XLIFF)
            ->setTargetFilePath($destination)
        ;

        $this->importer->import($data);

        return ['xliff' => file_get_contents($data->getTargetFilePath())];
    }
}
