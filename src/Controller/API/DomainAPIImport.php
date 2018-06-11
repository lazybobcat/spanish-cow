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
 * Time: 10:45
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

class DomainAPIImport extends Controller
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

    public function __invoke(\App\Entity\Import $data, Project $project, $domain, $locale)
    {
        /** @var Domain $domain */
        $domain = $project->getDomainByName($domain);

        if (!$domain || !$this->isGranted(DomainVoter::READ, $domain)) {
            throw $this->createNotFoundException();
        }

        $file = tempnam(sys_get_temp_dir(), 'spcow_api_import_');
        file_put_contents($file, $data->getXliff());

        $import = new Import();
        $import
            ->setSourceType(FileType::FILE_TYPE_XLIFF)
            ->setSourceFilePath($file)
            ->setLocaleCode($locale)
            ->setDomainName($domain->getName())
            ->setDomainId($domain->getId())
            ->setTargetType(FileType::FILE_TYPE_DATABASE)
            ->setKeep(false)
        ;

        $this->importer->import($import);

        return [];
    }
}
