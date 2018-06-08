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
 * Date: 08/06/18
 * Time: 16:18
 */

namespace App\Controller\API;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Entity\Project;
use App\Manager\AssetManager;
use App\Voter\DomainVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;

class AssetAPIPost extends Controller
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var AssetManager
     */
    private $assetManager;

    public function __construct(RequestStack $requestStack, AssetManager $assetManager)
    {
        $this->requestStack = $requestStack;
        $this->assetManager = $assetManager;
    }

    public function __invoke(Project $project, $domain)
    {
        /** @var Domain $domain */
        $domain = $project->getDomainByName($domain);

        if (!$domain || !$this->isGranted(DomainVoter::UPDATE, $domain)) {
            throw $this->createNotFoundException();
        }

        /** @var Asset $data */
        $data = $this->requestStack->getCurrentRequest()->attributes->get('data');
        /** @var Asset $asset */
        $asset = $this->assetManager->findOneBy(['resname' => $data->getResname(), 'domain' => $domain]);

        if ($asset) {
            // We do not override information that could have been changed by a user
//            $asset
//                ->setSource($data->getSource())
//                ->setNotes($data->getNotes())
//            ;
        } else {
            $asset = $data;
            $asset->setDomain($domain);
        }

        return $asset;
    }
}
