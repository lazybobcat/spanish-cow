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
 * Date: 06/06/18
 * Time: 18:20
 */

namespace App\Importer\Loader;

use App\Entity\Asset;
use App\Entity\Domain;
use App\Manager\DomainManager;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

class DatabaseLoader implements LoaderInterface
{
    /**
     * @var DomainManager
     */
    private $domainManager;

    public function __construct(DomainManager $domainManager)
    {
        $this->domainManager = $domainManager;
    }

    /**
     * Loads a locale.
     *
     * @param mixed  $domainId The Domain ID
     * @param string $locale   A locale
     * @param string $domain   The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($domainId, $locale, $domain = 'messages')
    {
        /** @var Domain $domain */
        $domain = $this->domainManager->find($domainId);
        $catalogue = new MessageCatalogue($locale);

        if (!$domain) {
            throw new \LogicException("The domain with id '{$domainId}' has not been found.");
        }

        $this->extract($domain, $catalogue, $locale);

        return $catalogue;
    }

    private function extract(Domain $domain, MessageCatalogue $catalogue, $locale)
    {
        /** @var Asset $asset */
        foreach ($domain->getAssets() as $asset) {
            $catalogue->set($asset->getResname(), $asset->translate($locale), $domain->getName());
        }
    }
}
