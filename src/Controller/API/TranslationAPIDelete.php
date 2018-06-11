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
 * Time: 09:48
 */

namespace App\Controller\API;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Project;
use App\Manager\AssetManager;
use App\Manager\TranslationManager;
use App\Voter\DomainVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

class TranslationAPIDelete extends Controller
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * @var TranslationManager
     */
    private $translationManager;

    public function __construct(RequestStack $requestStack, AssetManager $assetManager, TranslationManager $translationManager)
    {
        $this->requestStack = $requestStack;
        $this->assetManager = $assetManager;
        $this->translationManager = $translationManager;
    }

    public function __invoke(Project $project, $domain, $resname, $locale)
    {
        /** @var Domain $domain */
        $domain = $project->getDomainByName($domain);

        if (!$domain || !$domain->hasLocale($locale) || !$this->isGranted(DomainVoter::UPDATE, $domain)) {
            throw $this->createNotFoundException();
        }

        /** @var Asset $asset */
        $asset = $this->assetManager->findOneBy(['resname' => $resname, 'domain' => $domain]);

        if (!$asset) {
            throw $this->createNotFoundException();
        }

        $translation = $asset->getTranslationForCode($locale);

        if (!$translation) {
            throw $this->createNotFoundException();
        }

        $this->translationManager->delete($translation);

        return $translation;
    }
}
